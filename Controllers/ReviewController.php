<?php

require_once __DIR__ . '/../Model/Review.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class ReviewController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Review $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO reviews (customer_id, product_id, rating, comment) VALUES (:customer_id, :product_id, :rating, :comment)',
            [
                'customer_id' => $model->customer_id,
                'product_id' => $model->product_id,
                'rating' => $model->rating,
                'comment' => $model->comment,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, customer_id, product_id, rating, comment FROM reviews WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(Review $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE reviews SET
                 customer_id = :customer_id,
                 product_id = :product_id,
                 rating = :rating,
                 comment = :comment
             WHERE id = :id',
            [
                'id' => $model->id,
                'customer_id' => $model->customer_id,
                'product_id' => $model->product_id,
                'rating' => $model->rating,
                'comment' => $model->comment,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM reviews WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}

