<?php

class Review
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $customerId,
        int $productId,
        int $rating,
        ?string $comment
    ): int {
        $this->db->run_query(
            'INSERT INTO reviews (customer_id, product_id, rating, comment) VALUES (:customer_id, :product_id, :rating, :comment)',
            [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'rating' => $rating,
                'comment' => $comment,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, customer_id, product_id, rating, comment FROM reviews WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $customerId,
        int $productId,
        int $rating,
        ?string $comment
    ): bool {
        $result = $this->db->run_query(
            'UPDATE reviews SET
                 customer_id = :customer_id,
                 product_id = :product_id,
                 rating = :rating,
                 comment = :comment
             WHERE id = :id',
            [
                'id' => $id,
                'customer_id' => $customerId,
                'product_id' => $productId,
                'rating' => $rating,
                'comment' => $comment,
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
