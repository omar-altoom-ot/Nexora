<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. API FOR REAL-TIME JAVASCRIPT CHECK (Admin Coupons)
if (isset($_GET['check_code'])) {
    require_once __DIR__ . '/../../Config/DBconnection.php';
    require_once __DIR__ . '/../../Controllers/CouponController.php';
    $cc = new CouponController(new Db());
    echo $cc->checkCodeExists(trim($_GET['check_code'])) ? 'exists' : 'available';
    exit;
}

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        if (ob_get_length()) ob_clean();
        require __DIR__ . '/../Pages/error.php';
        exit;
    }
});

set_exception_handler(function($e) {
    http_response_code(500);
    error_log("Uncaught Exception: " . $e->getMessage());
    if (ob_get_length()) ob_clean();
    require __DIR__ . '/../Pages/error.php';
    exit;
});

function abort($code, $message = null) {
    http_response_code($code);
    if ($message) {
        $_SESSION['error_custom_message'] = $message;
    }
    if (ob_get_length()) ob_clean();
    require __DIR__ . '/../Pages/error.php';
    exit;
}

ob_start();

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
    'compare' => __DIR__ . '/../Pages/compare.php',
    'home' => __DIR__ . '/../Pages/home.php',
    'login' => __DIR__ . '/../Pages/login.php',
    'product' => __DIR__ . '/../Pages/product.php',
    'products' => __DIR__ . '/../Pages/products.php',
    'register' => __DIR__ . '/../Pages/register.php',
    'wishlist' => __DIR__ . '/../Pages/wishlist.php',
    'admin-order-details' => __DIR__ . '/../Pages/admin-order-details.php',
    'my_orders' => __DIR__ . '/../Pages/my_orders.php',
    'admin-notifications' => __DIR__ . '/../Pages/admin-notifications.php',
    'my-coupons' => __DIR__ . '/../Pages/my-coupons.php',
    'builder' => __DIR__ . '/../Pages/builder.php',
];

$page = $_GET['page'] ?? 'home';

if (!is_string($page)) {
    abort(400, "Invalid request format.");
}

if (!isset($routes[$page]) || !file_exists($routes[$page])) {
    abort(404);
}

$isProtectedAdminPage = str_starts_with($page, 'admin-') && $page !== 'admin-login';

if ($isProtectedAdminPage) {
    $isLoggedInAdmin = isset($_SESSION['admin_id']) && ($_SESSION['user_role'] ?? null) === 'admin';
    $isLoggedInCustomer = isset($_SESSION['customer_id']) && ($_SESSION['user_role'] ?? null) === 'customer';

    if (!$isLoggedInAdmin) {
        if ($isLoggedInCustomer) {
            // User is logged in, but they are a Customer, not an Admin -> 403 Forbidden
            abort(403, "You are logged in as a Customer. You do not have permission to view the Admin Console.");
        } else {
            // Not logged in at all -> Redirect to Admin Login
            header('Location: ../Layout/routing.php?page=admin-login');
            exit;
        }
    }
}

$isLoggedInCustomer = isset($_SESSION['customer_id']) && ($_SESSION['user_role'] ?? null) === 'customer';

if ($page === 'checkout' && !$isLoggedInCustomer) {
    $_SESSION['post_login_page'] = 'cart';
    header('Location: ../Layout/routing.php?page=login');
    exit;
}

$protectedCustomerPages = ['account'];

if (in_array($page, $protectedCustomerPages, true)) {
    if (!$isLoggedInCustomer) {
        header('Location: ../Layout/routing.php?page=login');
        exit;
    }

    require_once __DIR__ . '/../../Model/Customer.php';
    $db = new Db();
    $customerModel = new Customer($db);
    $customer = $customerModel->getCustomerById((int) $_SESSION['customer_id']);

    if (!$customer || (int) ($customer['is_active'] ?? 0) !== 1) {
        unset(
            $_SESSION['customer_id'],
            $_SESSION['customer_name'],
            $_SESSION['user_role'],
            $_SESSION['account_csrf_token']
        );
        session_regenerate_id(true);
        abort(403, "Your account has been deactivated. Please contact support.");
    }
}

if ($page === 'products') {
    require_once __DIR__ . '/../../Controllers/CatalogController.php';
    $db = new Db();
    $catalogRepository = new CatalogRepository($db);
    $catalogController = new CatalogController($catalogRepository);
    $catalogData = $catalogController->index($_GET);
}

require $routes[$page];

ob_end_flush();
?>