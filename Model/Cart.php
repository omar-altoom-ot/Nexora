<?php

class Cart
{
    public ?int $id = null;
    public ?int $customer_id = null;

    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart_items']) || !is_array($_SESSION['cart_items'])) {
            $_SESSION['cart_items'] = [];
        }
    }

    /** Move a signed-in customer's pre-session cart into the current session once. */
    private function importLegacyCart(int $customerId): void
    {
        if ($customerId <= 0) return;
        if (!isset($_SESSION['legacy_cart_imported'])) $_SESSION['legacy_cart_imported'] = [];
        if (!empty($_SESSION['legacy_cart_imported'][$customerId])) return;

        $this->db->beginTransaction();
        try {
            $this->db->run_query('SELECT id FROM customers WHERE id = ? FOR UPDATE', [$customerId]);
            $carts = $this->db->run_query('SELECT id FROM carts WHERE customer_id = ? ORDER BY id FOR UPDATE', [$customerId])->fetchAll();
            $stagedItems = [];
            if ($carts) {
                $cartIds = array_map(static fn(array $cart): int => (int) $cart['id'], $carts);
                $marks = implode(',', array_fill(0, count($cartIds), '?'));
                $legacyItems = $this->db->run_query(
                    "SELECT product_id, SUM(quantity) AS quantity, SUM(stock_reserved_quantity) AS reserved
                     FROM cart_items WHERE cart_id IN ($marks) GROUP BY product_id ORDER BY product_id",
                    $cartIds
                )->fetchAll();

                foreach ($legacyItems as $legacy) {
                    $productId = (int) $legacy['product_id'];
                    $quantity = (int) $legacy['quantity'];
                    $reserved = min($quantity, (int) $legacy['reserved']);
                    $unreserved = $quantity - $reserved;
                    if ($unreserved > 0) {
                        $this->db->run_query('SELECT id FROM products WHERE id = ? FOR UPDATE', [$productId]);
                        $reservedMore = $this->db->run_query(
                            'UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?',
                            [$unreserved, $productId, $unreserved]
                        );
                        if ($reservedMore->rowCount() === 1) $reserved += $unreserved;
                        else $quantity = $reserved;
                    }
                    if ($quantity > 0) $stagedItems[$productId] = ['quantity' => $quantity, 'stock_reserved_quantity' => $reserved];
                }

                $this->db->run_query("DELETE FROM cart_items WHERE cart_id IN ($marks)", $cartIds);
                $this->db->run_query("DELETE FROM carts WHERE id IN ($marks)", $cartIds);
            }
            $this->db->commit();

            foreach ($stagedItems as $productId => $legacyItem) {
                $current = $_SESSION['cart_items'][$productId] ?? ['quantity' => 0, 'stock_reserved_quantity' => 0];
                $_SESSION['cart_items'][$productId] = [
                    'quantity' => (int) ($current['quantity'] ?? 0) + $legacyItem['quantity'],
                    'stock_reserved_quantity' => (int) ($current['stock_reserved_quantity'] ?? 0) + $legacyItem['stock_reserved_quantity'],
                ];
            }
            $_SESSION['legacy_cart_imported'][$customerId] = true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    // Cart contents live in this PHP session for guests and signed-in customers.
    // Stock is still reserved transactionally in the database when an item is added.
    public function addItem(int $customerId, int $productId, int $quantity = 1): bool
    {
        if ($productId <= 0 || $quantity <= 0) return false;
        $this->importLegacyCart($customerId);
        $this->db->beginTransaction();
        try {
            $product = $this->db->run_query('SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE', [$productId])->fetch();
            if (!$product || (int) $product['stock_quantity'] < $quantity) {
                $this->db->rollBack();
                return false;
            }
            $changed = $this->db->run_query(
                'UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?',
                [$quantity, $productId, $quantity]
            );
            if ($changed->rowCount() !== 1) {
                $this->db->rollBack();
                return false;
            }
            $this->db->commit();

            $item = $_SESSION['cart_items'][$productId] ?? ['quantity' => 0, 'stock_reserved_quantity' => 0];
            $item['quantity'] = (int) ($item['quantity'] ?? 0) + $quantity;
            $item['stock_reserved_quantity'] = (int) ($item['stock_reserved_quantity'] ?? 0) + $quantity;
            $_SESSION['cart_items'][$productId] = $item;
            return true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function getItems(int $customerId): array
    {
        $this->importLegacyCart($customerId);
        $cart = $_SESSION['cart_items'] ?? [];
        if (!$cart) return [];
        $ids = array_values(array_filter(array_map('intval', array_keys($cart)), static fn(int $id): bool => $id > 0));
        if (!$ids) return [];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $products = $this->db->run_query(
            "SELECT id, name, price, stock_quantity FROM products WHERE id IN ($marks)",
            $ids
        )->fetchAll();
        $byId = [];
        foreach ($products as $product) $byId[(int) $product['id']] = $product;

        $items = [];
        foreach ($cart as $productId => $cartItem) {
            $productId = (int) $productId;
            if (!isset($byId[$productId]) || (int) ($cartItem['quantity'] ?? 0) <= 0) continue;
            $items[] = $byId[$productId] + [
                'quantity' => (int) $cartItem['quantity'],
                'stock_reserved_quantity' => (int) ($cartItem['stock_reserved_quantity'] ?? 0),
            ];
        }
        return $items;
    }

    public function updateQuantity(int $customerId, int $productId, int $quantity): bool
    {
        $this->importLegacyCart($customerId);
        $item = $_SESSION['cart_items'][$productId] ?? null;
        if ($productId <= 0 || $quantity <= 0 || !is_array($item)) return false;
        $oldQuantity = (int) ($item['quantity'] ?? 0);
        $oldReserved = min($oldQuantity, (int) ($item['stock_reserved_quantity'] ?? 0));
        $difference = $quantity - $oldQuantity;
        if ($difference === 0) return true;

        $this->db->beginTransaction();
        try {
            $product = $this->db->run_query('SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE', [$productId])->fetch();
            if (!$product) {
                $this->db->rollBack();
                return false;
            }
            if ($difference > 0) {
                $changed = $this->db->run_query('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?', [$difference, $productId, $difference]);
                if ($changed->rowCount() !== 1) {
                    $this->db->rollBack();
                    return false;
                }
                $newReserved = $oldReserved + $difference;
            } else {
                $newReserved = min($oldReserved, $quantity);
                $released = $oldReserved - $newReserved;
                if ($released > 0) $this->db->run_query('UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?', [$released, $productId]);
            }
            $this->db->commit();
            $item['quantity'] = $quantity;
            $item['stock_reserved_quantity'] = $newReserved;
            $_SESSION['cart_items'][$productId] = $item;
            return true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function removeItem(int $customerId, int $productId): bool
    {
        $this->importLegacyCart($customerId);
        $item = $_SESSION['cart_items'][$productId] ?? null;
        if ($productId <= 0 || !is_array($item)) return false;
        $reserved = max(0, (int) ($item['stock_reserved_quantity'] ?? 0));
        $this->db->beginTransaction();
        try {
            $product = $this->db->run_query('SELECT id FROM products WHERE id = ? FOR UPDATE', [$productId])->fetch();
            if (!$product) {
                $this->db->rollBack();
                return false;
            }
            if ($reserved > 0) $this->db->run_query('UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?', [$reserved, $productId]);
            $this->db->commit();
            unset($_SESSION['cart_items'][$productId]);
            return true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function clearCart(int $customerId): bool
    {
        $this->importLegacyCart($customerId);
        $cart = $_SESSION['cart_items'] ?? [];
        if (!$cart) return false;
        ksort($cart, SORT_NUMERIC);
        $this->db->beginTransaction();
        try {
            foreach ($cart as $productId => $item) {
                $productId = (int) $productId;
                $reserved = max(0, (int) ($item['stock_reserved_quantity'] ?? 0));
                $product = $this->db->run_query('SELECT id FROM products WHERE id = ? FOR UPDATE', [$productId])->fetch();
                if ($product && $reserved > 0) $this->db->run_query('UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?', [$reserved, $productId]);
            }
            $this->db->commit();
            $_SESSION['cart_items'] = [];
            return true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }
}
