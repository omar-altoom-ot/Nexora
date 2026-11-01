<?php

if (!isset($catalogData) || !is_array($catalogData)) {
    // Keep the page usable when opened directly, outside routing.php.
    require_once __DIR__ . '/../../Controllers/CatalogController.php';

    $catalogRepository = new CatalogRepository(new Db());
    $catalogData = (new CatalogController($catalogRepository))->index($_GET);
}

// Data received from CatalogController
$products = $catalogData['products'];
$search = $catalogData['search'] ?? '';

$categories = $catalogData['categories'] ?? [];

$categoryId = $catalogData['categoryId'] ?? null;


$totalProducts = $catalogData['totalProducts'];

$productPage = $catalogData['productPage'];
$totalPages = $catalogData['totalPages'];

$minPrice = $catalogData['minPrice'] ?? null;
$maxPrice = $catalogData['maxPrice'] ?? null;
$priceError = $catalogData['priceError'] ?? '';
$sort = $catalogData['sort'] ?? 'newest';


$paginationUrl = static function (int $targetPage) use (
    $search,
    $categoryId,
    $minPrice,
    $maxPrice,
    $sort
): string {

    $params = [
        'page' => 'products',
        'p' => $targetPage
    ];

    if ($search !== '') {
        $params['q'] = $search;
    }

    if ($categoryId !== null) {
        $params['category'] = $categoryId;
    }

    if ($minPrice !== null) {
        $params['min_price'] = $minPrice;
    }

    if ($maxPrice !== null) {
        $params['max_price'] = $maxPrice;
    }

    if ($sort !== 'newest') {
        $params['sort'] = $sort;
    }

    return '../Layout/routing.php?'
        . http_build_query($params);
};


// Page settings
$currentPage = 'products';
$pageTitle = 'Products — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = true;

require_once __DIR__ . '/../Includes/header.php';
?>

<?php require __DIR__ . '/../Layout/user_nav.php'; ?>

<main class="container page-main">

    <div class="page-heading">
        <div>
            <span class="eyebrow">// THE GEAR VAULT</span>

            <h1>
                Find your next
                <span class="text-grad">upgrade.</span>
            </h1>

            <p class="muted">
                Explore our latest products.
            </p>
        </div>

        <form method="GET" action="../Layout/routing.php" class="catalog-search-form">
            <input type="hidden" name="page" value="products">


            <?php if ($categoryId !== null): ?>
                <input type="hidden" name="category" value="<?= (int) $categoryId ?>">
            <?php endif; ?>


            <?php if ($minPrice !== null): ?>
                <input type="hidden" name="min_price"
                    value="<?= htmlspecialchars((string) $minPrice, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <?php if ($maxPrice !== null): ?>
                <input type="hidden" name="max_price"
                    value="<?= htmlspecialchars((string) $maxPrice, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>


            <input type="search" name="q" class="input catalog-search-input" placeholder="Search products..."
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">

            <button type="submit" class="btn btn-primary">
                Search
            </button>

            <a href="../Layout/routing.php?page=products" class="btn btn-ghost">
                Clear
            </a>
        </form>

    </div>



    <div class="catalog-shop-layout">

        <aside class="card catalog-category-panel">

            <h3>Categories</h3>

            <div class="catalog-category-list">

                <!-- All Products -->
                <a href="../Layout/routing.php?<?= htmlspecialchars(
                    http_build_query([
                        'page' => 'products',
                        'q' => $search
                    ]),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>" class="catalog-category-link <?= $categoryId === null ? 'selected' : '' ?>">
                    <span>All Products</span>
                </a>

                <!-- Categories from MySQL -->
                <?php foreach ($categories as $category): ?>

                    <?php
                    $id = (int) $category['id'];




                    $categoryUrl = '../Layout/routing.php?' .
                        http_build_query([
                            'page' => 'products',
                            'q' => $search,
                            'category' => $id,
                            'min_price' => $minPrice,
                            'max_price' => $maxPrice,
                            'sort' => $sort
                        ]);



                    ?>

                    <a href="<?= htmlspecialchars(
                        $categoryUrl,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>" class="catalog-category-link <?= $categoryId === $id ? 'selected' : '' ?>">
                        <span>
                            <?= htmlspecialchars(
                                $category['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <span class="catalog-category-count">
                            <?= (int) $category['product_count'] ?>
                        </span>
                    </a>

                <?php endforeach; ?>

            </div>

            <div class="catalog-price-filter">

                <h3>Price Range</h3>

                <form method="GET" action="../Layout/routing.php" class="catalog-price-form">

                    <input type="hidden" name="page" value="products">

                    <input type="hidden" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">

                    <?php if ($categoryId !== null): ?>
                        <input type="hidden" name="category" value="<?= (int) $categoryId ?>">
                    <?php endif; ?>

                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="catalog-price-fields">

                        <div class="field">
                            <label for="min-price">Min Price</label>

                            <input type="number" id="min-price" name="min_price" class="input" min="0" step="0.01"
                                placeholder="0.00"
                                value="<?= htmlspecialchars((string) ($minPrice ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label for="max-price">Max Price</label>

                            <input type="number" id="max-price" name="max_price" class="input" min="0" step="0.01"
                                placeholder="1000.00"
                                value="<?= htmlspecialchars((string) ($maxPrice ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                    </div>

                    <?php if ($priceError !== ''): ?>

                        <p class="catalog-price-error">
                            <?= htmlspecialchars($priceError, ENT_QUOTES, 'UTF-8') ?>
                        </p>

                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary catalog-apply-price">
                        Apply Price
                    </button>

                </form>

            </div>

        </aside>


        <section class="catalog-results">
      
<div class="catalog-toolbar">

    <div class="catalog-toolbar-info">
        <span>
            Total products:
            <?= (int) $totalProducts ?>
        </span>

        <span>
            Page <?= (int) $productPage ?>
            of <?= (int) $totalPages ?>
        </span>
    </div>

    <!-- Sorting Form -->
    <form
        method="GET"
        action="../Layout/routing.php"
        class="catalog-sort-form"
    >

        <input type="hidden" name="page" value="products">

        <input
            type="hidden"
            name="q"
            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
        >

        <?php if ($categoryId !== null): ?>
            <input
                type="hidden"
                name="category"
                value="<?= (int) $categoryId ?>"
            >
        <?php endif; ?>

        <?php if ($minPrice !== null): ?>
            <input
                type="hidden"
                name="min_price"
                value="<?= htmlspecialchars((string) $minPrice, ENT_QUOTES, 'UTF-8') ?>"
            >
        <?php endif; ?>

        <?php if ($maxPrice !== null): ?>
            <input
                type="hidden"
                name="max_price"
                value="<?= htmlspecialchars((string) $maxPrice, ENT_QUOTES, 'UTF-8') ?>"
            >
        <?php endif; ?>

        <label for="catalog-sort">Sort by:</label>

        <select
            id="catalog-sort"
            name="sort"
            class="input catalog-sort-select"
        >
            <option value="newest"
                <?= $sort === 'newest' ? 'selected' : '' ?>>
                Newest
            </option>

            <option value="price_asc"
                <?= $sort === 'price_asc' ? 'selected' : '' ?>>
                Price: Low to High
            </option>

            <option value="price_desc"
                <?= $sort === 'price_desc' ? 'selected' : '' ?>>
                Price: High to Low
            </option>

            <option value="name_asc"
                <?= $sort === 'name_asc' ? 'selected' : '' ?>>
                Name: A to Z
            </option>

            <option value="name_desc"
                <?= $sort === 'name_desc' ? 'selected' : '' ?>>
                Name: Z to A
            </option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">
            Apply
        </button>

    </form>

</div>


            <?php if (empty($products)): ?>

                <p class="muted">
                    No products available.
                </p>

            <?php else: ?>

                <div class="grid grid-4 product-grid">

                    <?php foreach ($products as $product): ?>

                        <?php
                        $productId = (int) $product['id'];

                        $productName = htmlspecialchars(
                            $product['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $categoryName = htmlspecialchars(
                            $product['category_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $price = number_format(
                            (float) $product['price'],
                            2
                        );

                        $stock = (int) $product['stock_quantity'];

                        // The catalog query returns the primary product_images.image
                        // value as image_path. Normalize common stored path formats
                        // while keeping image references inside the product uploads.
                        $imagePath = str_replace('\\', '/', trim((string) ($product['image_path'] ?? '')));
                        $imageUrl = '';

                        if (preg_match('~\Ahttps?://~i', $imagePath)) {
                            $imageUrl = $imagePath;
                        }

                        $imagePath = preg_replace('~^/?Nexora/~i', '', $imagePath);
                        $imagePath = ltrim($imagePath, '/');

                        if ($imageUrl === '' &&
                            str_starts_with($imagePath, 'Assets/Images/products/') &&
                            !str_contains($imagePath, '..')
                        ) {
                            $imageUrl = '/Nexora/' . $imagePath;
                        }

                        $detailsUrl =
                            '../Layout/routing.php?page=product&id='
                            . $productId;
                        ?>

                        <article class="card p-card">

                            <a class="p-img" href="<?= htmlspecialchars($detailsUrl, ENT_QUOTES, 'UTF-8') ?>">

                                <?php if ($imageUrl !== ''): ?>

                                    <img class="catalog-product-image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= $productName ?>" loading="lazy">

                                <?php else: ?>

                                    <span>📦</span>

                                <?php endif; ?>

                            </a>

                            <div class="p-body">

                                <span class="p-cat">
                                    <?= $categoryName ?>
                                </span>

                                <a class="p-name catalog-product-name"
                                    href="<?= htmlspecialchars($detailsUrl, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= $productName ?>
                                </a>

                                <div class="p-foot">

                                    <span class="p-price">
                                        <?= $price ?>
                                        <small>JD</small>
                                    </span>

                                    <?php if ($stock > 0): ?>

                                        <span class="status status-green">
                                            In Stock
                                        </span>

                                    <?php else: ?>

                                        <span class="status status-red">
                                            Out of Stock
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <a class="btn btn-primary btn-sm"
                                    href="<?= htmlspecialchars($detailsUrl, ENT_QUOTES, 'UTF-8') ?>">
                                    View Details
                                </a>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>
        
<?php if ($totalPages > 1): ?>

    <nav
        class="catalog-pagination"
        aria-label="Products pagination"
    >

        <!-- Previous -->
        <?php if ($productPage > 1): ?>

            <a
                class="catalog-page-link"
                href="<?= htmlspecialchars(
                    $paginationUrl($productPage - 1),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
                Previous
            </a>

        <?php endif; ?>

        <!-- Page Numbers -->
        <?php
        $startPage = max(1, $productPage - 2);
        $endPage = min($totalPages, $productPage + 2);
        ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

            <a
                class="catalog-page-link <?= $i === $productPage ? 'selected' : '' ?>"
                href="<?= htmlspecialchars(
                    $paginationUrl($i),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                <?= $i === $productPage ? 'aria-current="page"' : '' ?>
            >
                <?= $i ?>
            </a>

        <?php endfor; ?>

        <!-- Next -->
        <?php if ($productPage < $totalPages): ?>

            <a
                class="catalog-page-link"
                href="<?= htmlspecialchars(
                    $paginationUrl($productPage + 1),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
                Next
            </a>

        <?php endif; ?>

    </nav>

<?php endif; ?>


        </section>

    </div> <!-- End catalog-shop-layout -->

</main>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
