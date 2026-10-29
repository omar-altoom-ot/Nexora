<?php

class OrderItem
{
    public ?int $id = null;
    public int $order_id = 0;
    public ?int $product_id = null;
    public int $quantity = 1;
    public float $unit_price = 0.0;
    
    // Joined property for display
    public ?string $product_name = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->order_id = (int)($data['order_id'] ?? 0);
            $this->product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
            $this->quantity = (int)($data['quantity'] ?? 1);
            $this->unit_price = (float)($data['unit_price'] ?? 0);
            $this->product_name = $data['product_name'] ?? null;
        }
    }
}