<?php

class Order
{
    public ?int $id = null;
    public int $customer_id = 0;
    public ?string $order_date = null;
    public string $total_amount = '0.00';
    public string $status = 'pending';
    public string $shipping_address = '';
}
