<?php

require_once __DIR__ . '/../Model/Customer.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class CustomerController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Customer $model): int
    {
        $result = $this->db->run_query(
            'INSERT INTO customers (first_name, last_name, email, password_hash, phone) VALUES (:first_name, :last_name, :email, :password_hash, :phone)',
            [
                'first_name' => $model->first_name,
                'last_name' => $model->last_name,
                'email' => $model->email,
                'password_hash' => $model->password_hash,
                'phone' => $model->phone,
            ]
        );

        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, first_name, last_name, email, password_hash, phone FROM customers WHERE email = :email LIMIT 1',
            ['email' => $email]
        )->fetch();

        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, first_name, last_name, email, password_hash, phone FROM customers WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(Customer $model): bool
    {
        $result = $this->db->run_query(
            'UPDATE customers SET
                 first_name = :first_name,
                 last_name = :last_name,
                 email = :email,
                 password_hash = :password_hash,
                 phone = :phone
             WHERE id = :id',
            [
                'id' => $model->id,
                'first_name' => $model->first_name,
                'last_name' => $model->last_name,
                'email' => $model->email,
                'password_hash' => $model->password_hash,
                'phone' => $model->phone,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM customers WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}

