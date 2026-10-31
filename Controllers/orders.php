<?php

require_once __DIR__ . '/../Model/Order.php';

class OrderController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // NEW: Automatically advance order statuses based on time
    public function autoAdvanceOrderStatuses(): void
    {
        // 1. Pending -> Shipped (after 2 minutes)
        $sqlShipped = "
            UPDATE orders 
            SET status = 'shipped' 
            WHERE status = 'pending' 
            AND order_date <= NOW() - INTERVAL 2 MINUTE
        ";
        $this->db->run_query($sqlShipped);

        // 2. Shipped -> Delivered (after 2 more minutes, so 4 mins total)
        $sqlDelivered = "
            UPDATE orders 
            SET status = 'delivered' 
            WHERE status = 'shipped' 
            AND order_date <= NOW() - INTERVAL 4 MINUTE
        ";
        $this->db->run_query($sqlDelivered);
    }

    // READ: Get orders with filters and pagination (Returns array of Order objects)
    public function getOrdersFiltered(string $status = '', string $search = '', int $limit = 10, int $offset = 0): array
    {
        // CHANGED: Auto-advance statuses before fetching
        $this->autoAdvanceOrderStatuses();

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
        // CHANGED: Auto-advance statuses before counting
        $this->autoAdvanceOrderStatuses();
        
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
        // CHANGED: Auto-advance statuses before fetching
        $this->autoAdvanceOrderStatuses();

        $sql = "
            SELECT o.*, c.first_name, c.last_name, c.email, c.phone
            FROM orders o
            INNER JOIN customers c ON c.id = o.customer_id
            WHERE o.id = ?
        ";

        $result = $this->db->run_query($sql, [$id])->fetch();
        return $result ? new Order($result) : null;
    }

    // ... (Keep the rest of the methods exactly as they are) ...

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

    public function create(int $customerId, string $totalAmount, string $status, string $shippingAddress, ?string $orderDate = null): int 
    {
        $this->db->beginTransaction();

        try {
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
            
            $orderId = (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];

            $this->db->run_query(
                'INSERT INTO notifications (message) VALUES (?)', 
                ["New order #NX-{$orderId} placed by customer #{$customerId}."]
            );

            $this->db->commit();
            return $orderId;

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("Order Creation Failed: " . $e->getMessage());
            throw new Exception("Failed to save order in the database. " . $e->getMessage());
        }
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

    public function canUserCancel(Order $order): bool
    {
        if ($order->status !== 'pending') return false;
        
        $orderTime = strtotime($order->order_date);
        $secondsPassed = time() - $orderTime;
        
        return $secondsPassed <= 600;
    }

    public function getOrdersByCustomer(int $customerId): array
    {
        // CHANGED: Auto-advance statuses before fetching for the user
        $this->autoAdvanceOrderStatuses();

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

    public function markAsDeliveredByCustomer(int $orderId): bool
    {
        $order = $this->getOrderById($orderId);
        if ($order && $order->status === 'shipped') {
            return $this->updateOrderStatus($orderId, 'delivered');
        }
        return false;
    }
}