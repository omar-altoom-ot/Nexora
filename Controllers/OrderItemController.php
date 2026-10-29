<?php
require_once __DIR__ . '/../Model/OrderItem.php';

class OrderItemController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // READ: Get items for a specific order
    public function getByOrderId(int $orderId): array
    {
        $sql = "
            SELECT oi.*, p.name AS product_name
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
        ";
        $results = $this->db->run_query($sql, [$orderId])->fetchAll();
        return array_map(fn($row) => new OrderItem($row), $results);
    }

    // CREATE: Add an item to an order
    public function create(int $orderId, ?int $productId, int $quantity, float $unitPrice): int
    {
        $this->db->run_query(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)',
            [
                'order_id' => $orderId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]
        );
        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }
}