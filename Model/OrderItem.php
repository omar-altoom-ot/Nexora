<?php

class OrderItem
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $orderId,
        ?int $productId,
        int $quantity,
        string $unitPrice
    ): int {
        $this->db->run_query(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)',
            [
                'order_id' => $orderId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, order_id, product_id, quantity, unit_price FROM order_items WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $orderId,
        ?int $productId,
        int $quantity,
        string $unitPrice
    ): bool {
        $result = $this->db->run_query(
            'UPDATE order_items SET
                 order_id = :order_id,
                 product_id = :product_id,
                 quantity = :quantity,
                 unit_price = :unit_price
             WHERE id = :id',
            [
                'id' => $id,
                'order_id' => $orderId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM order_items WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}
