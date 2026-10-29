<?php

class Payment
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $orderId,
        string $amount,
        string $method,
        string $status
    ): int {
        $this->db->run_query(
            'INSERT INTO payments (order_id, amount, method, status) VALUES (:order_id, :amount, :method, :status)',
            [
                'order_id' => $orderId,
                'amount' => $amount,
                'method' => $method,
                'status' => $status,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, order_id, amount, method, status FROM payments WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $orderId,
        string $amount,
        string $method,
        string $status
    ): bool {
        $result = $this->db->run_query(
            'UPDATE payments SET
                 order_id = :order_id,
                 amount = :amount,
                 method = :method,
                 status = :status
             WHERE id = :id',
            [
                'id' => $id,
                'order_id' => $orderId,
                'amount' => $amount,
                'method' => $method,
                'status' => $status,
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
