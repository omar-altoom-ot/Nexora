<?php

class CategoryRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, name
                FROM categories
                ORDER BY name ASC";

        $stmt = $this->db->run_query($sql);

        return $stmt->fetchAll();
    }


    public function existsById(int $id): bool
    {
        $sql = "SELECT 1
            FROM categories
            WHERE id = :id
            LIMIT 1";

        $stmt = $this->db->run_query($sql, [
            'id' => $id
        ]);

        return $stmt->fetchColumn() !== false;
    }


}
