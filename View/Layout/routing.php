
<?php
// NEXORA Routing

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$routes = [
    'account' => __DIR__ . '/../Pages/account.php',
    'admin-categories' => __DIR__ . '/../Pages/admin-categories.php',
    'admin-coupons' => __DIR__ . '/../Pages/admin-coupons.php',
    'admin-customers' => __DIR__ . '/../Pages/admin-customers.php',
    'admin-login' => __DIR__ . '/../Pages/admin-login.php',
    'admin-orders' => __DIR__ . '/../Pages/admin-orders.php',
    'admin-overview' => __DIR__ . '/../Pages/admin-overview.php',
    'admin-products' => __DIR__ . '/../Pages/admin-products.php',
    'admin-profile' => __DIR__ . '/../Pages/admin-profile.php',
    'cart' => __DIR__ . '/../Pages/cart.php',
    'checkout' => __DIR__ . '/../Pages/checkout.php',
    'home' => __DIR__ . '/../Pages/home.php',
    'login' => __DIR__ . '/../Pages/login.php',
    'product' => __DIR__ . '/../Pages/product.php',
    'products' => __DIR__ . '/../Pages/products.php',
    'register' => __DIR__ . '/../Pages/register.php',
    'wishlist' => __DIR__ . '/../Pages/wishlist.php',
];

$page = $_GET['page'] ?? 'home';

if (!is_string($page) || !isset($routes[$page])) {
    http_response_code(404);
    exit('Page not found.');
}

// Check whether the requested page belongs to Admin.
$isProtectedAdminPage =
    str_starts_with($page, 'admin-')
    && $page !== 'admin-login';

if ($isProtectedAdminPage) {

    $isLoggedInAdmin =
        isset($_SESSION['admin_id'])
        && ($_SESSION['user_role'] ?? null) === 'admin';

    if (!$isLoggedInAdmin) {
        header(
            'Location: /Nexora/View/Layout/routing.php?page=admin-login'
        );
        exit;
    }
}

require $routes[$page];
