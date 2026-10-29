<?php

class Admin
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        string $name,
        string $email,
        string $passwordHash
    ): int {
        $this->db->run_query(
            'INSERT INTO admins (name, email, password_hash) VALUES (:name, :email, :password_hash)',
            [
                'name' => $name,
                'email' => $email,
                'password_hash' => $passwordHash,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, name, email, password_hash FROM admins WHERE email = :email LIMIT 1',
            ['email' => $email]
        )->fetch();

        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, name, email, password_hash FROM admins WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        string $name,
        string $email,
        string $passwordHash
    ): bool {
        $result = $this->db->run_query(
            'UPDATE admins SET
                 name = :name,
                 email = :email,
                 password_hash = :password_hash
             WHERE id = :id',
            [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'password_hash' => $passwordHash,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM admins WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}
