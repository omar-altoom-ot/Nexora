<?php

class Category
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        string $name,
        ?string $image
    ): int {
        $this->db->run_query(
            'INSERT INTO categories (name, image) VALUES (:name, :image)',
            [
                'name' => $name,
                'image' => $image,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
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

    public function update(
        int $id,
        string $name,
        ?string $image
    ): bool {
        $result = $this->db->run_query(
            'UPDATE categories SET
                 name = :name,
                 image = :image
             WHERE id = :id',
            [
                'id' => $id,
                'name' => $name,
                'image' => $image,
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
