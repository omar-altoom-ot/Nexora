<?php

require_once __DIR__ . '/../Model/Cart.php';
require_once __DIR__ . '/../Config/DBconnection.php';

class CartController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // ADD PRODUCT TO CART
    public function addItem(int $customerId, int $productId, int $quantity = 1): bool
    {
        $cart = new Cart($this->db);
        return $cart->addItem($customerId, $productId, $quantity);
    }

    // VIEW CART ITEMS
    public function getItems(int $customerId): array
    {
        $cart = new Cart($this->db);

        return $cart->getItems($customerId);
    }

    // UPDATE PRODUCT QUANTITY
    public function updateQuantity(int $customerId, int $productId, int $quantity): bool
    {
        $cart = new Cart($this->db);

        return $cart->updateQuantity($customerId, $productId, $quantity);
    }

    // REMOVE PRODUCT FROM CART
    public function removeItem(int $customerId, int $productId): bool
    {
        $cart = new Cart($this->db);

        return $cart->removeItem($customerId, $productId);
    }
}
