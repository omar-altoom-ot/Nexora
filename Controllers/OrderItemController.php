<?php

require_once __DIR__ . '/../Model/OrderItem.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class OrderItemController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(OrderItem $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)',
            [
                'order_id' => $model->order_id,
                'product_id' => $model->product_id,
                'quantity' => $model->quantity,
                'unit_price' => $model->unit_price,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, order_id, product_id, quantity, unit_price FROM order_items WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(OrderItem $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE order_items SET
                 order_id = :order_id,
                 product_id = :product_id,
                 quantity = :quantity,
                 unit_price = :unit_price
             WHERE id = :id',
            [
                'id' => $model->id,
                'order_id' => $model->order_id,
                'product_id' => $model->product_id,
                'quantity' => $model->quantity,
                'unit_price' => $model->unit_price,
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

