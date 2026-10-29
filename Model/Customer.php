
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../Config/DBconnection.php';

class Customer
{
    public ?int $id = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password_hash = '';
    public ?string $phone = null;
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Find customer by email
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->run_query(
            "SELECT *
             FROM customers
             WHERE email = ?
             LIMIT 1",
            [$email]
        );

        return $stmt->fetch();
    }

    // Register new customer
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

    // Get all customers for admin dashboard
    public function getAllCustomers(): array
    {
        $stmt = $this->db->run_query(
            "SELECT
                c.id,
                c.first_name,
                c.last_name,
                c.email,
                c.phone,
                c.is_active,
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
                 c.phone,
                 c.is_active

             ORDER BY c.id DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get customer details by ID
    public function getCustomerById(int $customerId): array|false
    {
        $stmt = $this->db->run_query(
            "SELECT
                id,
                first_name,
                last_name,
                email,
                phone,
                is_active
             FROM customers
             WHERE id = ?
             LIMIT 1",
            [$customerId]
        );

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update customer profile
    public function updateCustomer(
        int $customerId,
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone
    ): bool {

        $stmt = $this->db->run_query(
            "UPDATE customers
             SET first_name = ?,
                 last_name = ?,
                 email = ?,
                 phone = ?
             WHERE id = ?",
            [
                $firstName,
                $lastName,
                $email,
                $phone,
                $customerId
            ]
        );

        return $stmt->rowCount() > 0;
    }

    // Deactivate customer account
    public function deactivateCustomer(int $customerId): bool
    {
        $stmt = $this->db->run_query(
            "UPDATE customers
             SET is_active = 0
             WHERE id = ?
               AND is_active = 1",
            [$customerId]
        );

        return $stmt->rowCount() > 0;
    }

    // Reactivate customer account
    public function reactivateCustomer(int $customerId): bool
    {
        $stmt = $this->db->run_query(
            "UPDATE customers
             SET is_active = 1
             WHERE id = ?
               AND is_active = 0",
            [$customerId]
        );

        return $stmt->rowCount() > 0;
    }
}
