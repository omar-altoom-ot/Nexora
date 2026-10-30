
<?php

class Cart
{
    public ?int $id = null;
    public ?int $customer_id = null;

    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // ADD PRODUCT TO CART
    public function addItem(int $customerId, int $productId, int $quantity = 1): bool
    {
        if ($customerId <= 0 || $productId <= 0 || $quantity <= 0) {
            return false;
        }

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->run_query(
                'SELECT id, stock_quantity FROM products WHERE id = ? FOR UPDATE',
                [$productId]
            );

            $product = $stmt->fetch();

            if (!$product) {
                $this->db->rollBack();
                return false;
            }

            if ((int) $product['stock_quantity'] < $quantity) {
                $this->db->rollBack();
                return false;
            }

            $this->db->run_query(
                'INSERT INTO carts (customer_id)
            VALUES (?)
            ON DUPLICATE KEY UPDATE id = id',
                [$customerId]
            );

            $stmt = $this->db->run_query(
                'SELECT id FROM carts
            WHERE customer_id = ?
            ORDER BY id DESC LIMIT 1',
                [$customerId]
            );

            $cart = $stmt->fetch();
            if (!$cart) {
                $this->db->rollBack();
                return false;
            }


            $cartId = (int) $cart['id'];

            $this->db->run_query(
                'INSERT INTO cart_items (cart_id, product_id, quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                quantity = quantity + VALUES(quantity)',
                [$cartId, $productId, $quantity]
            );

            $stmt = $this->db->run_query(
                'SELECT quantity FROM cart_items
                WHERE cart_id = ? AND product_id = ?',
                [$cartId, $productId]
            );

            $item = $stmt->fetch();

            if (!$item || (int) $item['quantity'] > (int) $product['stock_quantity']) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    // VIEW CART ITEMS
    public function getItems(int $customerId): array
    {
        $stmt = $this->db->run_query(
            'SELECT p.id, p.name, p.price, ci.quantity
             FROM cart_items ci
             INNER JOIN products p ON ci.product_id = p.id
             INNER JOIN carts c ON ci.cart_id = c.id
             WHERE c.customer_id = ?',
            [$customerId]
        );

        return $stmt->fetchAll();
    }

    // UPDATE PRODUCT QUANTITY
    public function updateQuantity(int $customerId, int $productId, int $quantity): bool
    {
        if ($customerId <= 0 || $productId <= 0 || $quantity <= 0) {
            return false;
        }

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->run_query(
                'SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE',
                [$productId]
            );

            $product = $stmt->fetch();

            if (!$product || $quantity > (int) $product['stock_quantity']) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->run_query(
                'SELECT ci.id, ci.quantity
                FROM cart_items ci
                INNER JOIN carts c ON ci.cart_id = c.id
                WHERE c.customer_id = ? AND ci.product_id = ?
                FOR UPDATE',
                [$customerId, $productId]

            );

            $item = $stmt->fetch();

            if (!$item) {
                $this->db->rollBack();
                return false;
            }

            if ((int) $item['quantity'] === $quantity) {
                $this->db->commit();
                return true;
            }

            $stmt = $this->db->run_query(
                'UPDATE cart_items
                SET quantity = ?
                WHERE id = ?',
                [$quantity, $item['id']]
            );

            $updated = $stmt->rowCount() > 0;

            $this->db->commit();

            return $updated;
            
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    // REMOVE PRODUCT FROM CART
    public function removeItem(int $customerId, int $productId): bool
    {
        $stmt = $this->db->run_query(
            'DELETE ci FROM cart_items ci
             INNER JOIN carts c ON ci.cart_id = c.id
             WHERE c.customer_id = ? AND ci.product_id = ?',
            [$customerId, $productId]
        );

        return $stmt->rowCount() > 0;
    }
}
