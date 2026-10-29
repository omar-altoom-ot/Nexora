<?php

class Payment
{
    public ?int $id = null;
    public int $order_id = 0;
    public string $amount = '0.00';
    public string $method = '';
    public string $status = 'pending';
}
