<?php

class CartItem
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(
        int $cartId,
        int $productId,
        int $quantity
    ): int {
        $this->db->run_query(
            'INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)',
            [
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]
        );

        $result = $this->db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch();
        return (int) $result['id'];
    }

    public function findById(int $id): ?array
    {
        $result = $this->db->run_query(
            'SELECT id, cart_id, product_id, quantity FROM cart_items WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $result ?: null;
    }

    public function update(
        int $id,
        int $cartId,
        int $productId,
        int $quantity
    ): bool {
        $result = $this->db->run_query(
            'UPDATE cart_items SET
                 cart_id = :cart_id,
                 product_id = :product_id,
                 quantity = :quantity
             WHERE id = :id',
            [
                'id' => $id,
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM cart_items WHERE id = :id',
            ['id' => $id]
        );

        return $result->rowCount() > 0;
    }
}
