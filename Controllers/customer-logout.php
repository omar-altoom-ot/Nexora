
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

// Verify customer authentication
if (
    !isset($_SESSION['customer_id']) ||
    ($_SESSION['user_role'] ?? null) !== 'customer'
) {
    header('Location: /Nexora/View/Layout/routing.php?page=login');
    exit;
}

// Verify CSRF token
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['customer_logout_csrf_token']) ||
    !hash_equals($_SESSION['customer_logout_csrf_token'], $token)
) {
    http_response_code(403);
    exit('Invalid CSRF token.');
}

// Remove customer authentication data
unset(
    $_SESSION['customer_id'],
    $_SESSION['customer_name'],
    $_SESSION['user_role'],
    $_SESSION['customer_csrf_token'],
    $_SESSION['account_csrf_token'],
    $_SESSION['customer_logout_csrf_token']
);

// Generate a new session ID
session_regenerate_id(true);

// Redirect to login
header('Location: /Nexora/View/Layout/routing.php?page=login');
exit;
