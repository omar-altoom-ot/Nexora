<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../Model/Customer.php';

function sendResponse(int $status, bool $success, string $message): void
{
    http_response_code($status);

    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);

    exit;
}

// Only POST requests are allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    sendResponse(405, false, 'Method not allowed.');
}

// Protect against CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    sendResponse(403, false, 'Invalid CSRF token.');
}

// Read form data
$firstName = $_POST['first_name'] ?? '';
$lastName  = $_POST['last_name'] ?? '';
$email     = $_POST['email'] ?? '';
$password  = $_POST['password'] ?? '';
$phone     = $_POST['phone'] ?? '';

if (
    !is_string($firstName) ||
    !is_string($lastName) ||
    !is_string($email) ||
    !is_string($password) ||
    !is_string($phone)
) {
    sendResponse(400, false, 'Invalid input.');
}

$firstName = trim($firstName);
$lastName = trim($lastName);
$email = strtolower(trim($email));
$phone = trim($phone);

// Validation
if ($firstName === '' || $lastName === '') {
    sendResponse(422, false, 'First and last name are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(422, false, 'Invalid email address.');
}

if (strlen($password) < 8) {
    sendResponse(422, false, 'Password must be at least 8 characters.');
}

try {
    $db = new Db();
    $customer = new Customer($db);

    // Check duplicate email
    if ($customer->findByEmail($email)) {
        sendResponse(409, false, 'This email is already registered.');
    }

    // Save customer using existing Model
    $registered = $customer->register(
        $firstName,
        $lastName,
        $email,
        $password,
        $phone !== '' ? $phone : null
    );

    if (!$registered) {
        sendResponse(422, false, 'Unable to create account.');
    }

    // Sign in the new customer, same as existing register flow
    $newCustomer = $customer->findByEmail($email);

    if (!$newCustomer) {
        sendResponse(201, true, 'Account created. Please sign in.');
    }

    session_regenerate_id(true);

    $_SESSION['customer_id'] = (int) $newCustomer['id'];

    $_SESSION['customer_name'] =
        $newCustomer['first_name'] . ' ' . $newCustomer['last_name'];

    $_SESSION['user_role'] = 'customer';

    unset($_SESSION['post_login_page']);

    // Rotate CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    sendResponse(201, true, 'Registration successful.');

} catch (PDOException $e) {

    if ((string) $e->getCode() === '23000') {
        sendResponse(409, false, 'This email is already registered.');
    }

    error_log($e->getMessage());
    sendResponse(500, false, 'Database error.');

} catch (Throwable $e) {

    error_log($e->getMessage());
    sendResponse(500, false, 'Server error.');
}