<?php
require_once __DIR__ . '/../Model/Coupon.php';

class CouponController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // READ: Get all coupons
    public function getAll(): array
    {
        $sql = "SELECT * FROM coupons ORDER BY id DESC";
        $results = $this->db->run_query($sql)->fetchAll();
        return array_map(fn($row) => new Coupon($row), $results);
    }

    // READ: Get one coupon by ID
    public function getById(int $id): ?Coupon
    {
        $sql = "SELECT * FROM coupons WHERE id = ?";
        $result = $this->db->run_query($sql, [$id])->fetch();
        return $result ? new Coupon($result) : null;
    }

    // CREATE: Add a new coupon
    // CREATE: Add a new coupon
    public function create(string $code, string $type, float $value, float $minAmount, string $validUntil, int $limit): bool|int
    {
        // SECURITY FIX: Check if the date is in the past
        $today = date('Y-m-d');
        if ($validUntil < $today) {
            return false; // Reject the submission
        }

        $sql = "INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, valid_until, usage_limit) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->run_query($sql, [$code, $type, $value, $minAmount, $validUntil, $limit]);
        return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    }

    // UPDATE: Toggle active status
    public function toggleStatus(int $id, bool $status): bool
    {
        $sql = "UPDATE coupons SET is_active = ? WHERE id = ?";
        return $this->db->run_query($sql, [(int)$status, $id])->rowCount() > 0;
    }

    // DELETE: Remove a coupon
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM coupons WHERE id = ?";
        return $this->db->run_query($sql, [$id])->rowCount() > 0;
    }
}