
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

// Admin authentication
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

// Customer authentication
$protectedCustomerPages = [
    'account',
    'checkout'
];

if (in_array($page, $protectedCustomerPages, true)) {

    $isLoggedInCustomer =
        isset($_SESSION['customer_id'])
        && ($_SESSION['user_role'] ?? null) === 'customer';

    if (!$isLoggedInCustomer) {
        header(
            'Location: /Nexora/View/Layout/routing.php?page=login'
        );
        exit;
    }

    require_once __DIR__ . '/../../Model/Customer.php';

    $db = new Db();
    $customerModel = new Customer($db);

    $customer = $customerModel->getCustomerById(
        (int) $_SESSION['customer_id']
    );

    // Block deleted or deactivated accounts
    if (
        !$customer ||
        (int) ($customer['is_active'] ?? 0) !== 1
    ) {
        unset(
            $_SESSION['customer_id'],
            $_SESSION['customer_name'],
            $_SESSION['user_role'],
            $_SESSION['account_csrf_token']
        );

        session_regenerate_id(true);

        header(
            'Location: /Nexora/View/Layout/routing.php?page=login'
        );
        exit;
    }
}

// Load requested page
require $routes[$page];
