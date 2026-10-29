<?php

require_once __DIR__ . '/../Model/Admin.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class AdminController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Admin $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO admins (name, email, password_hash) VALUES (:name, :email, :password_hash)',
            [
                'name' => $model->name,
                'email' => $model->email,
                'password_hash' => $model->password_hash,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
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

    public function update(Admin $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE admins SET
                 name = :name,
                 email = :email,
                 password_hash = :password_hash
             WHERE id = :id',
            [
                'id' => $model->id,
                'name' => $model->name,
                'email' => $model->email,
                'password_hash' => $model->password_hash,
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

