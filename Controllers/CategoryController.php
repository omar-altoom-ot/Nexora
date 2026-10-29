<?php

require_once __DIR__ . '/../Model/Category.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class CategoryController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Category $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO categories (name, image) VALUES (:name, :image)',
            [
                'name' => $model->name,
                'image' => $model->image,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findByName(string $name): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, name, image FROM categories WHERE name = :name LIMIT 1',
            ['name' => $name]
        )->fetch();

        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, name, image FROM categories WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(Category $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE categories SET
                 name = :name,
                 image = :image
             WHERE id = :id',
            [
                'id' => $model->id,
                'name' => $model->name,
                'image' => $model->image,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM categories WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}

