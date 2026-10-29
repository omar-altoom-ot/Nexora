<?php

require_once __DIR__ . '/../Model/Product.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class ProductController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Product $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO products (category_id, name, description, price, stock_quantity) VALUES (:category_id, :name, :description, :price, :stock_quantity)',
            [
                'category_id' => $model->category_id,
                'name' => $model->name,
                'description' => $model->description,
                'price' => $model->price,
                'stock_quantity' => $model->stock_quantity,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, category_id, name, description, price, stock_quantity FROM products WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(Product $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE products SET
                 category_id = :category_id,
                 name = :name,
                 description = :description,
                 price = :price,
                 stock_quantity = :stock_quantity
             WHERE id = :id',
            [
                'id' => $model->id,
                'category_id' => $model->category_id,
                'name' => $model->name,
                'description' => $model->description,
                'price' => $model->price,
                'stock_quantity' => $model->stock_quantity,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM products WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}

