<?php

class Customer
{
    public ?int $id = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password_hash = '';
    public ?string $phone = null;
}
