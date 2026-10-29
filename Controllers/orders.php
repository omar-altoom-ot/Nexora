<?php

// Include the Model so the Controller can create Order objects
require_once __DIR__ . '/../Model/Order.php';

class OrderController
{
	private Db $db;

	public function __construct(Db $db)
	{
		$this->db = $db;
	}

	// ==========================================
	// --- ADMIN DASHBOARD QUERIES ---
	// ==========================================

	// READ: Get orders with filters and pagination (Returns array of Order objects)
	public function getOrdersFiltered(string $status = '', string $search = '', int $limit = 10, int $offset = 0): array
	{
		$limit  = max(1, min($limit, 100));
		$offset = max(0, $offset);

		$where  = [];
		$params = [];

		if ($status !== '' && $status !== 'all') {
			$where[] = 'o.status = :status';
			$params[':status'] = $status;
		}

		if ($search !== '') {
			$where[] = '(CONCAT(c.first_name, " ", c.last_name) LIKE :s1
						OR c.email LIKE :s2
						OR o.id LIKE :s3)';
			$like = '%' . $search . '%';
			$params[':s1'] = $like;
			$params[':s2'] = $like;
			$params[':s3'] = $like;
		}

		$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

		$sql = "
			SELECT
				o.id, o.customer_id, o.order_date, o.total_amount, o.status, o.shipping_address,
				c.first_name, c.last_name, c.email,
				(SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.order_id = o.id) AS items_count
			FROM orders o
			INNER JOIN customers c ON c.id = o.customer_id
			$whereSql
			ORDER BY o.order_date DESC, o.id DESC
			LIMIT {$limit} OFFSET {$offset}
		";

		$results = $this->db->run_query($sql, $params)->fetchAll();
		
		// Convert database arrays into Order Objects
		return array_map(fn($row) => new Order($row), $results);
	}

	// READ: Count orders for pagination
	public function countOrdersFiltered(string $status = '', string $search = ''): int
	{
		$where  = [];
		$params = [];

		if ($status !== '' && $status !== 'all') {
			$where[] = 'o.status = :status';
			$params[':status'] = $status;
		}

		if ($search !== '') {
			$where[] = '(CONCAT(c.first_name, " ", c.last_name) LIKE :s1 OR c.email LIKE :s2 OR o.id LIKE :s3)';
			$like = '%' . $search . '%';
			$params[':s1'] = $like;
			$params[':s2'] = $like;
			$params[':s3'] = $like;
		}

		$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

		$sql = "SELECT COUNT(*) FROM orders o INNER JOIN customers c ON c.id = o.customer_id $whereSql";

		return (int) $this->db->run_query($sql, $params)->fetchColumn();
	}

	// READ: Get counts for all statuses (for tabs)
	public function getStatusCounts(): array
	{
		$rows = $this->db->run_query("SELECT status, COUNT(*) AS c FROM orders GROUP BY status")->fetchAll();
		
		$out = ['all' => 0];
		foreach ($rows as $r) {
			$out[$r['status']] = (int) $r['c'];
			$out['all'] += (int) $r['c'];
		}
		return $out;
	}

	// READ: Get one order (Returns a single Order Object)
	public function getOrderById(int $id): ?Order
	{
		$sql = "
			SELECT o.*, c.first_name, c.last_name, c.email, c.phone
			FROM orders o
			INNER JOIN customers c ON c.id = o.customer_id
			WHERE o.id = ?
		";

		$result = $this->db->run_query($sql, [$id])->fetch();
		return $result ? new Order($result) : null;
	}

	// READ: Get the items belonging to an order (Returns raw arrays for easy looping)
	public function getOrderItems(int $orderId): array
	{
		$sql = "
			SELECT oi.product_id, oi.quantity, oi.unit_price, p.name AS product_name
			FROM order_items oi
			LEFT JOIN products p ON p.id = oi.product_id
			WHERE oi.order_id = ?
		";

		return $this->db->run_query($sql, [$orderId])->fetchAll();
	}

	// ==========================================
	// --- CRUD METHODS (From your partner) ---
	// ==========================================

	// public function create(int $customerId, string $totalAmount, string $status, string $shippingAddress, ?string $orderDate = null): int 
	// {
	// 	$this->db->run_query(
	// 		'INSERT INTO orders (customer_id, order_date, total_amount, status, shipping_address) VALUES (:customer_id, COALESCE(:order_date, CURRENT_TIMESTAMP), :total_amount, :status, :shipping_address)',
	// 		[
	// 			'customer_id' => $customerId, 'order_date' => $orderDate, 'total_amount' => $totalAmount,
	// 			'status' => $status, 'shipping_address' => $shippingAddress,
	// 		]
	// 	);
	// 	return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
	// }

    public function create(int $customerId, string $totalAmount, string $status, string $shippingAddress, ?string $orderDate = null): int 
    {
        // 1. Insert the order
        $this->db->run_query(
            'INSERT INTO orders (customer_id, order_date, total_amount, status, shipping_address) VALUES (:customer_id, COALESCE(:order_date, CURRENT_TIMESTAMP), :total_amount, :status, :shipping_address)',
            [
                'customer_id' => $customerId, 
                'order_date' => $orderDate, 
                'total_amount' => $totalAmount,
                'status' => $status, 
                'shipping_address' => $shippingAddress,
            ]
        );
        
        // 2. Get the new Order ID
        $orderId = (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];

        // 3. Create the admin notification
        $this->db->run_query(
            'INSERT INTO notifications (message) VALUES (?)', 
            ["New order #NX-{$orderId} placed by customer #{$customerId}."]
        );

        // 4. Return the ID so the caller can use it (e.g., to add order items)
        return $orderId;
    }

	public function update(int $id, int $customerId, string $orderDate, string $totalAmount, string $status, string $shippingAddress): bool 
	{
		$result = $this->db->run_query(
			'UPDATE orders SET customer_id = :customer_id, order_date = :order_date, total_amount = :total_amount, status = :status, shipping_address = :shipping_address WHERE id = :id',
			[
				'id' => $id, 'customer_id' => $customerId, 'order_date' => $orderDate, 
				'total_amount' => $totalAmount, 'status' => $status, 'shipping_address' => $shippingAddress,
			]
		);
		return $result->rowCount() > 0;
	}

	// chnage the status should be delivered , shipped , canclled
	public function updateOrderStatus(int $id, string $status): bool 
	{
		$allowedStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
		if (!in_array($status, $allowedStatuses, true)) return false;
		
		return $this->db->run_query("UPDATE orders SET status = ? WHERE id = ?", [$status, $id])->rowCount() > 0;
	}

	public function delete(int $id): bool 
	{
		return $this->db->run_query('DELETE FROM orders WHERE id = :id', ['id' => $id])->rowCount() > 0;
	}

	    // USER: Check if user can cancel (5 hour window)
    public function canUserCancel(Order $order): bool
    {
        if ($order->status !== 'pending' && $order->status !== 'confirmed') return false;
        $orderTime = strtotime($order->order_date);
        $hoursPassed = (time() - $orderTime) / 3600;
        return $hoursPassed <= 5;
    }

    // USER: Get all orders for a specific customer
    public function getOrdersByCustomer(int $customerId): array
    {
        $sql = "
            SELECT o.*, 
            (SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.order_id = o.id) AS items_count
            FROM orders o 
            WHERE o.customer_id = ? 
            ORDER BY o.order_date DESC
        ";
        $results = $this->db->run_query($sql, [$customerId])->fetchAll();
        return array_map(fn($row) => new Order($row), $results);
    }

    // USER: Mark order as delivered
    public function markAsDeliveredByCustomer(int $orderId): bool
    {
        $order = $this->getOrderById($orderId);
        if ($order && $order->status === 'shipped') {
            return $this->updateOrderStatus($orderId, 'delivered');
        }
        return false;
    }
}


?>