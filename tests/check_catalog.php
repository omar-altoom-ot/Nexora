<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../Model/CatalogRepository.php';

$db = new Db();

$catalog = new CatalogRepository($db);

$products = $catalog->findActiveProducts();

echo "Active products: " . count($products) . PHP_EOL;

foreach ($products as $product) {
    echo $product['id'] . " - " . $product['name'] . PHP_EOL;
}


echo PHP_EOL . "Search Results:" . PHP_EOL;

$searchResults = $catalog->searchProducts('Gaming');

echo "Matching products: "
    . count($searchResults)
    . PHP_EOL;

foreach ($searchResults as $product) {
    echo $product['id']
        . " - "
        . $product['name']
        . PHP_EOL;
}



echo PHP_EOL . "Category Results:" . PHP_EOL;

// Replace 1 with an existing category ID
$categoryId = 1;

$categoryProducts = $catalog->findByCategory($categoryId);

echo "Matching products: "
    . count($categoryProducts)
    . PHP_EOL;

foreach ($categoryProducts as $product) {
    echo $product['id']
        . " - "
        . $product['name']
        . " - "
        . $product['category_name']
        . PHP_EOL;
}


echo PHP_EOL . "Price Filter Results:" . PHP_EOL;

$minPrice = 10;
$maxPrice = 50;

$priceProducts = $catalog->findByPriceRange(
    $minPrice,
    $maxPrice
);

echo "Matching products: "
    . count($priceProducts)
    . PHP_EOL;

foreach ($priceProducts as $product) {
    echo $product['id']
        . " - "
        . $product['name']
        . " - "
        . $product['price']
        . " JD"
        . PHP_EOL;
}


echo PHP_EOL . "Combined Filters:" . PHP_EOL;

// Test 1: All active products
$all = $catalog->findFilteredProducts();

echo "All active: " . count($all) . PHP_EOL;

// Test 2: Search + Category
$filtered = $catalog->findFilteredProducts(
    'Gaming',
    1
);

echo "Gaming in category 1: "
    . count($filtered)
    . PHP_EOL;

// Test 3: Price range
$priceFiltered = $catalog->findFilteredProducts(
    '',
    null,
    10,
    50
);

echo "Price 10-50 JD: "
    . count($priceFiltered)
    . PHP_EOL;

foreach ($priceFiltered as $product) {
    echo $product['name']
        . " - "
        . $product['price']
        . " JD"
        . PHP_EOL;
}

// Test 4: Nonexistent category
$missingCategory = $catalog->findFilteredProducts(
    '',
    999999
);

echo "Nonexistent category: "
    . count($missingCategory)
    . PHP_EOL;


echo PHP_EOL . "Sorting Test:" . PHP_EOL;

// Low to High
$productsAsc = $catalog->findFilteredProducts(
    '',
    null,
    null,
    null,
    'price_asc'
);

echo PHP_EOL . "Price ASC:" . PHP_EOL;

foreach ($productsAsc as $product) {
    echo $product['name']
        . " - "
        . $product['price']
        . " JD"
        . PHP_EOL;
}

// High to Low
$productsDesc = $catalog->findFilteredProducts(
    '',
    null,
    null,
    null,
    'price_desc'
);

echo PHP_EOL . "Price DESC:" . PHP_EOL;

foreach ($productsDesc as $product) {
    echo $product['name']
        . " - "
        . $product['price']
        . " JD"
        . PHP_EOL;
}


echo PHP_EOL . "Pagination Test:" . PHP_EOL;

$perPage = 3;

for ($page = 1; $page <= 3; $page++) {

    $offset = ($page - 1) * $perPage;

    $products = $catalog->findFilteredProducts(
        '',
        null,
        null,
        null,
        'newest',
        $perPage,
        $offset
    );

    echo PHP_EOL . "Page $page:" . PHP_EOL;

    echo "Products: " . count($products) . PHP_EOL;

    foreach ($products as $product) {
        echo $product['id']
            . " - "
            . $product['name']
            . PHP_EOL;
    }

    
}



echo PHP_EOL . "Catalog Final Tests:" . PHP_EOL;

// Test COUNT
$total = $catalog->countFilteredProducts();
$gamingCount = $catalog->countFilteredProducts('Gaming');

echo "Total active: $total" . PHP_EOL;
echo "Gaming count: $gamingCount" . PHP_EOL;

// Test categories
$categories = $catalog->getCategoriesWithCounts();

foreach ($categories as $category) {
    echo "Category: "
        . $category['name']
        . " - Products: "
        . $category['product_count']
        . PHP_EOL;
}

// Test product images
$products = $catalog->findFilteredProducts();

foreach ($products as $product) {

    echo PHP_EOL . $product['name'];

    echo PHP_EOL . "Image: "
        . ($product['image_path'] ?? 'No image');

    echo PHP_EOL . "Category: "
        . $product['category_name'];

    echo PHP_EOL;
}
