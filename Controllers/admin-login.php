
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/Admin.php';

$errors = [];
$email = '';

if (empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['admin_csrf_token'], $submittedToken)
    ) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errors[] = 'Please enter a valid email and password.';
    } else {
        try {
            $db = new Db();
            $adminModel = new Admin($db);

            $admin = $adminModel->findByEmail($email);

            if (!$admin || !password_verify($password, $admin['password_hash'])) {
                $errors[] = 'Invalid email or password.';
            } else {
                session_regenerate_id(true);

                $_SESSION['admin_id'] = (int) $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['user_role'] = 'admin';

                header('Location: /Nexora/View/Layout/routing.php?page=admin-overview');
                exit;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = 'A database error occurred. Please try again later.';
        }
    }
}
