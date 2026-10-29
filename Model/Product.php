<?php

class Product
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $categoryId,
        string $name,
        ?string $description,
        string $price,
        int $stockQuantity
    ): int {
        $this->db->run_query(
            'INSERT INTO products (category_id, name, description, price, stock_quantity) VALUES (:category_id, :name, :description, :price, :stock_quantity)',
            [
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stockQuantity,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, category_id, name, description, price, stock_quantity FROM products WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $categoryId,
        string $name,
        ?string $description,
        string $price,
        int $stockQuantity
    ): bool {
        $result = $this->db->run_query(
            'UPDATE products SET
                 category_id = :category_id,
                 name = :name,
                 description = :description,
                 price = :price,
                 stock_quantity = :stock_quantity
             WHERE id = :id',
            [
                'id' => $id,
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stockQuantity,
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
