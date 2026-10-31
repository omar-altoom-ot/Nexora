<?php

require_once __DIR__ . '/../Model/Wishlist.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class WishlistController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Wishlist $model): int
    {
        $this->db->run_query(
            'INSERT INTO wishlists (customer_id) VALUES (:customer_id)',
            ['customer_id' => $model->customer_id]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findByCustomerId(int $customer_id): ?array
    {
        $query = $this->db->run_query(
            'SELECT id, customer_id FROM wishlists WHERE customer_id = :customer_id LIMIT 1',
            ['customer_id' => $customer_id]
        );

        return $query->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $query = $this->db->run_query(
            'SELECT id, customer_id FROM wishlists WHERE id = :id LIMIT 1',
            ['id' => $id]
        );

        return $query->fetch() ?: null;
    }

    public function update(Wishlist $model): bool
    {
        $query = $this->db->run_query(
            'UPDATE wishlists SET customer_id = :customer_id WHERE id = :id',
            ['id' => $model->id, 'customer_id' => $model->customer_id]
        );

        return $query->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $query = $this->db->run_query(
            'DELETE FROM wishlists WHERE id = :id',
            ['id' => $id]
        );

        return $query->rowCount() > 0;
    }
}

