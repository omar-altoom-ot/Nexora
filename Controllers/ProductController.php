<?php

class ProductController
{
    private ProductRepository $products;
    private CategoryRepository $categories;

    public function __construct(
        ProductRepository $products,
        CategoryRepository $categories
    ) {
        $this->products = $products;
        $this->categories = $categories;
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

        } elseif (mb_strlen(trim($name), 'UTF-8') > 150) {
            $errors['name'] = 'Product name must not exceed 150 characters.';
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


        $categoryId = $input['category_id'] ?? null;

        $validCategoryId = filter_var(
            $categoryId,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($validCategoryId === false) {
            $errors['category_id'] = 'Please select a valid category.';

        } elseif (!$this->categories->existsById($validCategoryId)) {
            $errors['category_id'] = 'Selected category does not exist.';
        }

        return $errors;
    }

    public function isValidCsrfToken(
        mixed $submittedToken,
        mixed $expectedToken
    ): bool {
        if (
            !is_string($submittedToken) ||
            !is_string($expectedToken) ||
            $submittedToken === '' ||
            $expectedToken === ''
        ) {
            return false;
        }

        return hash_equals($expectedToken, $submittedToken);
    }


}