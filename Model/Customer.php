<?php

class Customer
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $passwordHash,
        ?string $phone = null
    ): int {
        $this->db->run_query(
            'INSERT INTO customers (first_name, last_name, email, password_hash, phone)
             VALUES (:first_name, :last_name, :email, :password_hash, :phone)',
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password_hash' => $passwordHash,
                'phone' => $phone,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, first_name, last_name, email, password_hash, phone
             FROM customers
             WHERE id = :id
             LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, first_name, last_name, email, password_hash, phone
             FROM customers
             WHERE email = :email
             LIMIT 1',
            ['email' => $email]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone = null
    ): bool {
        $result = $this->db->run_query(
            'UPDATE customers
             SET first_name = :first_name,
                 last_name = :last_name,
                 email = :email,
                 phone = :phone
             WHERE id = :id',
            [
                'id' => $id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
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
