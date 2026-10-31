<?php

class Coupon
{
    public ?int $id = null;
    public string $code = '';
    public string $discount_type = 'percentage';
    public float $discount_value = 0.0;
    public float $min_order_amount = 0.0;
    public string $valid_until = '';
    public int $usage_limit = 100;
    public int $used_count = 0;
    public bool $is_active = true;
    public ?string $created_at = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->code = $data['code'] ?? '';
            $this->discount_type = $data['discount_type'] ?? 'percentage';
            $this->discount_value = (float)($data['discount_value'] ?? 0);
            $this->min_order_amount = (float)($data['min_order_amount'] ?? 0);
            $this->valid_until = $data['valid_until'] ?? '';
            $this->usage_limit = (int)($data['usage_limit'] ?? 100);
            $this->used_count = (int)($data['used_count'] ?? 0);
            $this->is_active = (bool)($data['is_active'] ?? true);
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}