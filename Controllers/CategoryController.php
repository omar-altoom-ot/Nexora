<?php

require_once __DIR__ . '/../Config/DBconnection.php';

class CategoryController
{
    private Db $db;

    public function __construct(?Db $db = null)
    {
        $this->db = $db ?? new Db();
    }

    public function getAll(): array
    {
        return $this->db->run_query(
            'SELECT c.id, c.name, c.image, COUNT(p.id) AS product_count
             FROM categories AS c
             LEFT JOIN products AS p ON p.category_id = c.id
             GROUP BY c.id, c.name, c.image
             ORDER BY c.name ASC'
        )->fetchAll();
    }

    public function getCategoryProducts(int $id): array
    {
        if ($id < 1) return [];
        return $this->db->run_query(
            'SELECT p.*, c.name AS category_name FROM products AS p
             INNER JOIN categories AS c ON c.id = p.category_id
             WHERE c.id = :id ORDER BY p.name ASC', ['id' => $id]
        )->fetchAll();
    }

    public function createCategory(string $name, ?string $image = null): int
    {
        $name = trim($name);
        $this->validateName($name);
        $this->db->run_query(
            'INSERT INTO categories (name, image) VALUES (:name, :image)',
            ['name' => $name, 'image' => $image]
        );
        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function updateCategory(int $id, string $name, ?string $image = null): bool
    {
        $name = trim($name);
        if ($id < 1) throw new InvalidArgumentException('Choose a valid category.');
        $this->validateName($name);
        return $this->db->run_query(
            'UPDATE categories SET name = :name, image = COALESCE(:image, image) WHERE id = :id',
            ['id' => $id, 'name' => $name, 'image' => $image]
        )->rowCount() > 0;
    }

    public function deleteCategory(int $id): bool
    {
        if ($id < 1) return false;
        return $this->db->run_query(
            'DELETE FROM categories WHERE id = :id', ['id' => $id]
        )->rowCount() > 0;
    }

    private function validateName(string $name): void
    {
        if ($name === '' || mb_strlen($name) > 100) {
            throw new InvalidArgumentException('Category name must be between 1 and 100 characters.');
        }
    }
}
