<?php

class Product
{
    public ?int $id = null;
    public int $category_id = 0;
    public string $name = '';
    public ?string $description = null;
    public string $price = '0.00';
    public int $stock_quantity = 0;
}
