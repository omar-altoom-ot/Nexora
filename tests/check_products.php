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



require_once __DIR__ . '/../Controllers/ProductImageController.php';

$imageController = new ProductImageController($db);
$missing = $imageController->validateImage(null);

echo "Missing image: ";
var_dump(isset($missing['product_image']));

$uploadError = $imageController->validateImage([
    'error' => UPLOAD_ERR_INI_SIZE
]);

echo "Upload error: ";
var_dump(isset($uploadError['product_image']));

try {
    $imageController->storeImage(null);

    echo "Invalid image rejected: bool(false)" . PHP_EOL;

} catch (InvalidArgumentException $e) {

    echo "Invalid image rejected: bool(true)" . PHP_EOL;
}

try {
    $imageController->saveImageRecord(0, '');

    echo "Invalid image record rejected: bool(false)" . PHP_EOL;

} catch (InvalidArgumentException $e) {

    echo "Invalid image record rejected: bool(true)" . PHP_EOL;
}


$db->beginTransaction();

echo "Transaction started: ";
var_dump($db->inTransaction());

$db->rollBack();

echo "Transaction ended: ";
var_dump(!$db->inTransaction());

try {
    $imageController->replacePrimaryImage(0, 'test.jpg');

    echo "Invalid ID rejected: false";

} catch (InvalidArgumentException $e) {
    echo "Invalid ID rejected: true";
}


echo PHP_EOL . "=== Search & Filters Tests ===" . PHP_EOL;

$inactiveProducts = $productRepository->findPage(
    10,
    0,
    '',
    null,
    'inactive'
);

$inactiveCount = $productRepository->countFilteredProducts(
    '',
    null,
    'inactive'
);

echo "Inactive products count: ";
var_dump($inactiveCount);

echo "Pagination count valid: ";
var_dump(count($inactiveProducts) <= 10);

$allInactive = true;

foreach ($inactiveProducts as $product) {
    if ((int) $product['is_active'] !== 0) {
        $allInactive = false;
        break;
    }
}

echo "Status filter valid: ";
var_dump($allInactive);

// Additional test: filtered count matches results
// when all matching products fit in one page.
if ($inactiveCount <= 10) {
    echo "Filtered count matches: ";
    var_dump(count($inactiveProducts) === $inactiveCount);
}
echo "Invalid ID rejected: true" . PHP_EOL;