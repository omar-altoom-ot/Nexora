<?php

class Order
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $customerId,
        string $totalAmount,
        string $status,
        string $shippingAddress,
        ?string $orderDate = null
    ): int {
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

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, customer_id, order_date, total_amount, status, shipping_address FROM orders WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $customerId,
        string $orderDate,
        string $totalAmount,
        string $status,
        string $shippingAddress
    ): bool {
        $result = $this->db->run_query(
            'UPDATE orders SET
                 customer_id = :customer_id,
                 order_date = :order_date,
                 total_amount = :total_amount,
                 status = :status,
                 shipping_address = :shipping_address
             WHERE id = :id',
            [
                'id' => $id,
                'customer_id' => $customerId,
                'order_date' => $orderDate,
                'total_amount' => $totalAmount,
                'status' => $status,
                'shipping_address' => $shippingAddress,
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

