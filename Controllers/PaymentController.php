<?php

require_once __DIR__ . '/../Model/Payment.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class PaymentController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Payment $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO payments (order_id, amount, method, status) VALUES (:order_id, :amount, :method, :status)',
            [
                'order_id' => $model->order_id,
                'amount' => $model->amount,
                'method' => $model->method,
                'status' => $model->status,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, order_id, amount, method, status FROM payments WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(Payment $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE payments SET
                 order_id = :order_id,
                 amount = :amount,
                 method = :method,
                 status = :status
             WHERE id = :id',
            [
                'id' => $model->id,
                'order_id' => $model->order_id,
                'amount' => $model->amount,
                'method' => $model->method,
                'status' => $model->status,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM payments WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}

