
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../Config/DBconnection.php';

class Customer
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->run_query(
            "SELECT * FROM customers WHERE email = ? LIMIT 1",
            [$email]
        );

        return $stmt->fetch();
    }

    public function register(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        ?string $phone = null
    ): bool {

        $email = strtolower(trim($email));

        if (
            trim($firstName) === '' ||
            trim($lastName) === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($password) < 8
        ) {
            return false;
        }

        if ($this->findByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->db->run_query(
            "INSERT INTO customers
             (first_name, last_name, email, password_hash, phone)
             VALUES (?, ?, ?, ?, ?)",
            [
                trim($firstName),
                trim($lastName),
                $email,
                $hashedPassword,
                $phone !== null && trim($phone) !== ''
                    ? trim($phone)
                    : null
            ]
        );

        return $stmt->rowCount() === 1;
    }

    

public function getAllCustomers(): array
{
    $stmt = $this->db->run_query(
        "SELECT
            c.id,
            c.first_name,
            c.last_name,
            c.email,
            c.phone,
            COUNT(o.id) AS orders_count,
            COALESCE(
                SUM(
                    CASE
                        WHEN o.status <> 'cancelled'
                        THEN o.total_amount
                        ELSE 0
                    END
                ),
                0
            ) AS lifetime_value

         FROM customers c

         LEFT JOIN orders o
             ON o.customer_id = c.id

         GROUP BY
             c.id,
             c.first_name,
             c.last_name,
             c.email,
             c.phone

         ORDER BY c.id DESC"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
