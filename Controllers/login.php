
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/Customer.php';

$errors = [];
$email = '';

if (empty($_SESSION['customer_csrf_token'])) {
    $_SESSION['customer_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['customer_csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    $emailInput = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $email = is_string($emailInput)
        ? strtolower(trim($emailInput))
        : '';

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !is_string($password) ||
        $password === ''
    ) {
        $errors[] = 'Please enter a valid email and password.';
    } else {

        try {
            $db = new Db();
            $customerModel = new Customer($db);

            $customer = $customerModel->findByEmail($email);

            if (
                !$customer ||
                !password_verify(
                    $password,
                    $customer['password_hash']
                )
            ) {
                $errors[] = 'Invalid email or password.';

            } elseif ((int) ($customer['is_active'] ?? 0) !== 1) {

                $errors[] = 'Your account has been deactivated. Please contact support.';

            } else {

                session_regenerate_id(true);

                $_SESSION['customer_id'] = (int) $customer['id'];
                $_SESSION['customer_name'] =
                    $customer['first_name'] . ' ' . $customer['last_name'];

                $_SESSION['user_role'] = 'customer';

                header(
                    'Location: ../View/Layout/routing.php?page=account'
                );
                exit;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = 'Unable to sign in. Please try again later.';
        }
    }
}
