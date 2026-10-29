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
}
