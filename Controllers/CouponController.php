<?php
require_once __DIR__ . '/../Model/Coupon.php';

class CouponController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // ADMIN: Get all coupons with optional filter (all, valid, expired)
    public function getAdminCoupons(string $filter = 'all', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT * FROM coupons";
        
        if ($filter === 'valid') {
            $sql .= " WHERE is_active = 1 AND valid_until >= CURDATE() AND used_count < usage_limit";
        } elseif ($filter === 'expired') {
            $sql .= " WHERE is_active = 0 OR valid_until < CURDATE() OR used_count >= usage_limit";
        }

        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";

        $results = $this->db->run_query($sql)->fetchAll();
        return array_map(fn($row) => new Coupon($row), $results);
    }

    // ADMIN: Count total coupons for pagination based on filter
    public function countAdminCoupons(string $filter = 'all'): int
    {
        $sql = "SELECT COUNT(*) as total FROM coupons";
        
        if ($filter === 'valid') {
            $sql .= " WHERE is_active = 1 AND valid_until >= CURDATE() AND used_count < usage_limit";
        } elseif ($filter === 'expired') {
            $sql .= " WHERE is_active = 0 OR valid_until < CURDATE() OR used_count >= usage_limit";
        }

        $result = $this->db->run_query($sql)->fetch();
        return (int) $result['total'];
    }

    public function getById(int $id): ?Coupon
    {
        $sql = "SELECT * FROM coupons WHERE id = ?";
        $result = $this->db->run_query($sql, [$id])->fetch();
        return $result ? new Coupon($result) : null;
    }

    public function create(string $code, string $type, float $value, float $minAmount, string $validUntil, int $limit): bool|int|string
    {
        $today = date('Y-m-d');
        if ($validUntil < $today) {
            return false;
        }

        $sql = "INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, valid_until, usage_limit) VALUES (?, ?, ?, ?, ?, ?)";

        try {
            $this->db->run_query($sql, [$code, $type, $value, $minAmount, $validUntil, $limit]);
            return (int) $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return 'duplicate'; 
            }
            throw $e;
        }
    }

    public function toggleStatus(int $id, bool $status): bool
    {
        $sql = "UPDATE coupons SET is_active = ? WHERE id = ?";
        return $this->db->run_query($sql, [(int)$status, $id])->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM coupons WHERE id = ?";
        return $this->db->run_query($sql, [$id])->rowCount() > 0;
    }

    public function getPublicActive(): array
    {
        $sql = "SELECT * FROM coupons
                WHERE is_active = 1
                AND valid_until >= CURDATE()
                AND used_count < usage_limit
                ORDER BY discount_value DESC";
        $results = $this->db->run_query($sql)->fetchAll();
        return array_map(fn($row) => new Coupon($row), $results);
    }

    // USER: Get ONLY valid coupons for the user (with Pagination)
    public function getUserCoupons(int $userId, int $limit = 6, int $offset = 0): array
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT * FROM coupons
                WHERE is_active = 1
                AND valid_until >= CURDATE()
                AND used_count < usage_limit
                ORDER BY id ASC
                LIMIT $limit OFFSET $offset";
                
        $results = $this->db->run_query($sql)->fetchAll();
        return array_map(fn($row) => new Coupon($row), $results);
    }

    // USER: Count total valid coupons for the user (for Pagination)
    public function countUserCoupons(int $userId): int
    {
        $sql = "SELECT COUNT(*) as total FROM coupons
                WHERE is_active = 1
                AND valid_until >= CURDATE()
                AND used_count < usage_limit";
                
        $result = $this->db->run_query($sql)->fetch();
        return (int) $result['total'];
    }

    public function getByCode(string $code): ?Coupon
    {
        $sql = "SELECT * FROM coupons WHERE code = ? LIMIT 1";
        $result = $this->db->run_query($sql, [$code])->fetch();
        return $result ? new Coupon($result) : null;
    }

    public function checkCodeExists(string $code): bool
    {
        $sql = "SELECT id FROM coupons WHERE code = ? LIMIT 1";
        $result = $this->db->run_query($sql, [$code])->fetch();
        return $result ? true : false;
    }
}