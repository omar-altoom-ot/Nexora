<?php

require_once __DIR__ . '/../Model/WishlistItem.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class WishlistItemController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(WishlistItem $model): bool
    {
        $result = $this->db->run_query(
            'INSERT INTO wishlist_items (wishlist_id, product_id)
             VALUES (:wishlist_id, :product_id)',
            [
                'wishlist_id' => $model->wishlist_id,
                'product_id' => $model->product_id,
            ]
        );

        return $result->rowCount() > 0;
    }

    public function find(int $wishlistId, int $productId): ?array
    {
        $result = $this->db->run_query(
            'SELECT wishlist_id, product_id FROM wishlist_items
             WHERE wishlist_id = :wishlist_id AND product_id = :product_id LIMIT 1',
            ['wishlist_id' => $wishlistId, 'product_id' => $productId]
        )->fetch();

        return $result ?: null;
    }

    public function findByWishlistId(int $wishlistId): array
    {
        return $this->db->run_query(
            'SELECT wishlist_id, product_id FROM wishlist_items WHERE wishlist_id = :wishlist_id',
            ['wishlist_id' => $wishlistId]
        )->fetchAll();
    }

    public function delete(int $wishlistId, int $productId): bool
    {
        $result = $this->db->run_query(
            'DELETE FROM wishlist_items WHERE wishlist_id = :wishlist_id AND product_id = :product_id',
            ['wishlist_id' => $wishlistId, 'product_id' => $productId]
        );

        return $result->rowCount() > 0;
    }
}

