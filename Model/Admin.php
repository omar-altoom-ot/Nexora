<?php

require_once __DIR__ . '/../Config/DBconnection.php';

class Admin
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public string $password_hash = '';

    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->run_query(
            "SELECT id, name, email, password_hash
             FROM admins
             WHERE email = ?
             LIMIT 1",
            [$email]
        );

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}