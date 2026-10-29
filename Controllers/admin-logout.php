
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !isset($_SESSION['admin_csrf_token']) ||
    !hash_equals($_SESSION['admin_csrf_token'], $token)
) {
    http_response_code(403);
    exit('Invalid CSRF token.');
}

if (
    !isset($_SESSION['admin_id']) ||
    ($_SESSION['user_role'] ?? null) !== 'admin'
) {
    http_response_code(403);
    exit('Access denied.');
}

// Remove only Admin session data.
unset(
    $_SESSION['admin_id'],
    $_SESSION['admin_name'],
    $_SESSION['user_role'],
    $_SESSION['admin_csrf_token']
);

session_regenerate_id(true);

header('Location: /Nexora/View/Layout/routing.php?page=admin-login');
exit;
