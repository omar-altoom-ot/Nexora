<?php

class ProductRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }


    public function findAll(): array
    {
        $sql = "SELECT p.*, c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON p.category_id = c.id
            ORDER BY p.id DESC";

        $stmt = $this->db->run_query($sql);

        return $stmt->fetchAll();

    }


    public function create(array $data): int
    {
        $sql = "INSERT INTO products
                (category_id, name, description, price, stock_quantity)
            VALUES
                (:category_id, :name, :description, :price, :stock_quantity)";

        $this->db->run_query($sql, [
            'category_id' => (int) $data['category_id'],
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => (int) $data['stock_quantity']
        ]);

        return (int) $this->db
            ->run_query("SELECT LAST_INSERT_ID()")
            ->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT p.*, c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON p.category_id = c.id
            WHERE p.id = :id
            LIMIT 1";

        $stmt = $this->db->run_query($sql, [
            'id' => $id
        ]);

        $product = $stmt->fetch();

        return $product === false ? null : $product;
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE products
            SET
                category_id = :category_id,
                name = :name,
                description = :description,
                price = :price,
                stock_quantity = :stock_quantity
            WHERE id = :id";

        $this->db->run_query($sql, [
            'id' => $id,
            'category_id' => (int) $data['category_id'],
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => (int) $data['stock_quantity']
        ]);
    }


    public function deactivate(int $id): bool
    {
        $sql = "UPDATE products
            SET is_active = 0
            WHERE id = :id
              AND is_active = 1";

        $stmt = $this->db->run_query($sql, [
            'id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}