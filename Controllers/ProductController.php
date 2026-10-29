<?php

class ProductController
{
    private ProductRepository $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    public function index(): array
    {
        return $this->products->findAll();
    }


    public function validateCreateInput(array $input): array
    {
        $errors = [];

        $name = $input['name'] ?? null;

        if (!is_string($name) || trim($name) === '') {
            $errors['name'] = 'Product name is required.';
        }

        $price = $input['price'] ?? null;

        if (
            
            !is_string($price) ||
            !preg_match('/\A[0-9]{1,8}(\.[0-9]{1,2})?\z/', $price) ||
            (float) $price <= 0
        ) {
            $errors['price'] = 'Enter a valid price greater than zero.';
        }

        $stock = $input['stock_quantity'] ?? null;

        $validStock = filter_var(
            $stock,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0]]
        );

        if ($validStock === false) {
            $errors['stock_quantity'] = 'Stock must be a non-negative integer.';
        }



        return $errors;
    }



}