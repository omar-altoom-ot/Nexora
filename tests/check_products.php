<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../Config/DBconnection.php';
require_once __DIR__ . '/../Model/ProductRepository.php';
require_once __DIR__ . '/../Controllers/ProductController.php';

$db = new Db();

$productRepository = new ProductRepository($db);

$controller = new ProductController($productRepository);

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