
<?php
// Abdullah ALqudah


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../Model/Customer.php';

$errors = [];
$success = '';

$firstName = '';
$lastName = '';
$email = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['csrf_token'], $submittedToken)
    ) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($firstName === '' || $lastName === '') {
        $errors[] = 'First name and last name are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (empty($errors)) {
        try {
            $db = new Db();
            $customer = new Customer($db);

            if ($customer->findByEmail($email)) {
                $errors[] = 'This email is already registered.';
            } else {
                $registered = $customer->register(
                    $firstName,
                    $lastName,
                    $email,
                    $password,
                    $phone !== '' ? $phone : null
                );

                if ($registered) {

                    $newCustomer = $customer->findByEmail($email);

                    if ($newCustomer) {

                        session_regenerate_id(true);

                        $_SESSION['customer_id'] = (int) $newCustomer['id'];

                        $_SESSION['customer_name'] =
                            $newCustomer['first_name'] . ' ' . $newCustomer['last_name'];

                        $_SESSION['user_role'] = 'customer';

                        $postLoginPage = 'home';

                        unset($_SESSION['post_login_page']);

                        header('Location: routing.php?page=' . $postLoginPage);
                        exit;
                    } else {
                        $errors[] = 'Account created, but automatic sign-in failed.';
                    }
                } else {
                    $errors[] = 'Unable to create account.';
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'This email is already registered.';
            } else {
                error_log($e->getMessage());
                $errors[] = 'A database error occurred.';
            }
        }
    }
}
