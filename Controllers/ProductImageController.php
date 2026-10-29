<?php

require_once __DIR__ . '/../Model/ProductImage.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class ProductImageController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(ProductImage $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO product_images (product_id, image) VALUES (:product_id, :image)',
            [
                'product_id' => $model->product_id,
                'image' => $model->image,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, product_id, image FROM product_images WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(ProductImage $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE product_images SET
                 product_id = :product_id,
                 image = :image
             WHERE id = :id',
            [
                'id' => $model->id,
                'product_id' => $model->product_id,
                'image' => $model->image,
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

