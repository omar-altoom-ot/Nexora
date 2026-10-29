<?php

class Review
{
    public ?int $id = null;
    public int $customer_id = 0;
    public int $product_id = 0;
    public int $rating = 0;
    public ?string $comment = null;
}
