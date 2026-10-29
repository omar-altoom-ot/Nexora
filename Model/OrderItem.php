<?php

class OrderItem
{
    public ?int $id = null;
    public int $order_id = 0;
    public ?int $product_id = null;
    public int $quantity = 1;
    public string $unit_price = '0.00';
}
