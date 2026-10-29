<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../Config/DBconnection.php';
require_once __DIR__ . '/../Model/ProductRepository.php';
require_once __DIR__ . '/../Controllers/ProductController.php';
require_once __DIR__ . '/../Model/CategoryRepository.php';

$db = new Db();

$productRepository = new ProductRepository($db);

$categoryRepository = new CategoryRepository($db);

$controller = new ProductController(
    $productRepository,
    $categoryRepository
);

$products = $controller->index();

echo "Products found: " . count($products) . PHP_EOL;

echo "Test 1: Empty name" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => ''
]));

echo "Test 2: Valid name" . PHP_EOL;

echo "Test 3: Valid product data" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => 'ASUS Laptop',
    'price' => '750.00',
    'stock_quantity' => '5'
]));

echo "Test 4: Invalid price" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => 'ASUS Laptop',
    'price' => '12.345',
    'stock_quantity' => '5'
]));

echo "Test 5: Invalid stock" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => 'ASUS Laptop',
    'price' => '750.00',
    'stock_quantity' => '-3'
]));

echo "Test: Valid Category ID" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => 'ASUS Laptop',
    'price' => '750.00',
    'stock_quantity' => '5',
    'category_id' => '1'
]));

echo "Test: Invalid Category ID" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => 'ASUS Laptop',
    'price' => '750.00',
    'stock_quantity' => '5',
    'category_id' => '0'
]));


$categories = $categoryRepository->findAll();

if (!empty($categories)) {
    $existingId = (int) $categories[0]['id'];

    var_dump($categoryRepository->existsById($existingId));
}

var_dump($categoryRepository->existsById(0));

echo "Test: Non-existing Category" . PHP_EOL;

print_r($controller->validateCreateInput([
    'name' => 'ASUS Laptop',
    'price' => '750.00',
    'stock_quantity' => '5',
    'category_id' => '999999'
]));

if (!empty($categories)) {
    $validCategoryId = (string) $categories[0]['id'];

    foreach ([150, 151] as $length) {
        echo "Name length: $length" . PHP_EOL;

        print_r($controller->validateCreateInput([
            'name' => str_repeat('A', $length),
            'price' => '750.00',
            'stock_quantity' => '5',
            'category_id' => $validCategoryId
        ]));
    }
}

$testToken = bin2hex(random_bytes(32));

echo "Test: Valid CSRF Token" . PHP_EOL;

var_dump($controller->isValidCsrfToken(
    $testToken,
    $testToken
));

echo "Test: Invalid CSRF Token" . PHP_EOL;

var_dump($controller->isValidCsrfToken(
    'wrong-token',
    $testToken
));

echo "Test: Missing CSRF Token" . PHP_EOL;

var_dump($controller->isValidCsrfToken(
    null,
    $testToken
));