<?php

class ProductImage
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $productId,
        string $image
    ): int {
        $this->db->run_query(
            'INSERT INTO product_images (product_id, image) VALUES (:product_id, :image)',
            [
                'product_id' => $productId,
                'image' => $image,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, product_id, image FROM product_images WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $productId,
        string $image
    ): bool {
        $result = $this->db->run_query(
            'UPDATE product_images SET
                 product_id = :product_id,
                 image = :image
             WHERE id = :id',
            [
                'id' => $id,
                'product_id' => $productId,
                'image' => $image,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM product_images WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}
