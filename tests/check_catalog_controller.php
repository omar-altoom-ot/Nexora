<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../Controllers/CatalogController.php';

$db = new Db();

$repository = new CatalogRepository($db);

$controller = new CatalogController($repository);

// Test pages 1, 2, and 3
for ($page = 1; $page <= 3; $page++) {

    $data = $controller->index([
        'p' => $page
    ]);

    echo PHP_EOL . "Page: "
        . $data['productPage']
        . "/"
        . $data['totalPages']
        . PHP_EOL;

    echo "Products: "
        . count($data['products'])
        . PHP_EOL;

    foreach ($data['products'] as $product) {
        echo $product['id']
            . " - "
            . $product['name']
            . PHP_EOL;
    }
}

echo PHP_EOL . "Categories: "
    . count($data['categories'])
    . PHP_EOL;

    
echo PHP_EOL . "Search + Category Test:" . PHP_EOL;

$data = $controller->index([
    'q' => 'Gaming',
    'category' => '1',
    'p' => 2
]);

echo "Search: " . $data['search'] . PHP_EOL;

echo "Category: " . $data['categoryId'] . PHP_EOL;

echo "Matching products: "
    . $data['totalProducts']
    . PHP_EOL;

echo "Current page: "
    . $data['productPage']
    . "/"
    . $data['totalPages']
    . PHP_EOL;

foreach ($data['products'] as $product) {
    echo $product['id']
        . " - "
        . $product['name']
        . PHP_EOL;
}

$data = $controller->index([
    'q' => 'ProductDoesNotExist',
    'category' => '1'
]);

echo count($data['products']);


echo PHP_EOL . "Price + Sorting Test:" . PHP_EOL;

$data = $controller->index([
    'min_price' => '10',
    'max_price' => '50',
    'sort' => 'price_asc',
    'p' => 1
]);

echo "Matching products: "
    . $data['totalProducts']
    . PHP_EOL;

echo "Sort: "
    . $data['sort']
    . PHP_EOL;

echo "Page: "
    . $data['productPage']
    . "/"
    . $data['totalPages']
    . PHP_EOL;

foreach ($data['products'] as $product) {
    echo $product['name']
        . " - "
        . $product['price']
        . " JD"
        . PHP_EOL;
}

// Invalid price range test
echo PHP_EOL . "Invalid Price Test:" . PHP_EOL;

$invalid = $controller->index([
    'min_price' => '50',
    'max_price' => '10'
]);

echo "Validation: "
    . $invalid['priceError']
    . PHP_EOL;
