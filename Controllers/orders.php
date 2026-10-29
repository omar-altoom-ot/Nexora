<?php

require_once __DIR__ . '/../Model/Order.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class OrderController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Order $model): int
    {
        if ($model->order_date === null) {
            $this->db->run_query(
                'INSERT INTO orders (customer_id, total_amount, status, shipping_address) VALUES (:customer_id, :total_amount, :status, :shipping_address)',
                [
                    'customer_id' => $model->customer_id,
                'total_amount' => $model->total_amount,
                'status' => $model->status,
                'shipping_address' => $model->shipping_address,
                ]
            );
        } else {
            $this->db->run_query(
                'INSERT INTO orders (customer_id, order_date, total_amount, status, shipping_address)
                 VALUES (:customer_id, :order_date, :total_amount, :status, :shipping_address)',
                [
                    'customer_id' => $model->customer_id,
                'order_date' => $model->order_date,
                'total_amount' => $model->total_amount,
                'status' => $model->status,
                'shipping_address' => $model->shipping_address,
                ]
            );
        }

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, customer_id, order_date, total_amount, status, shipping_address FROM orders WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(Order $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE orders SET
                 customer_id = :customer_id,
                 order_date = :order_date,
                 total_amount = :total_amount,
                 status = :status,
                 shipping_address = :shipping_address
             WHERE id = :id',
            [
                'id' => $model->id,
                'customer_id' => $model->customer_id,
                'order_date' => $model->order_date,
                'total_amount' => $model->total_amount,
                'status' => $model->status,
                'shipping_address' => $model->shipping_address,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM orders WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}

