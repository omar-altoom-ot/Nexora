<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../Config/DBconnection.php';
require_once __DIR__ . '/../Model/ProductRepository.php';
require_once __DIR__ . '/../Model/CategoryRepository.php';
require_once __DIR__ . '/../Controllers/ProductController.php';

$db = new Db();

$products = new ProductRepository($db);
$categories = new CategoryRepository($db);

$controller = new ProductController($products, $categories);



$existingProducts = $controller->index();

if ($existingProducts !== []) {
    $existingId = (int) $existingProducts[0]['id'];

    $found = $productRepository->findById($existingId);

    echo "Existing product found: ";
    var_dump($found !== null);
}

$missing = $productRepository->findById(0);

echo "Missing product returns null: ";
var_dump($missing === null);