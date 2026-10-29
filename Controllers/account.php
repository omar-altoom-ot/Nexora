
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/Customer.php';

// Check customer authentication.
if (
    !isset($_SESSION['customer_id']) ||
    ($_SESSION['user_role'] ?? null) !== 'customer'
) {
    header('Location: /Nexora/View/Layout/routing.php?page=login');
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

$customer = null;
$errors = [];
$success = '';
$formData = [];

if (empty($_SESSION['account_csrf_token'])) {
    $_SESSION['account_csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $db = new Db();
    $customerModel = new Customer($db);

    $customer = $customerModel->getCustomerById($customerId);

    // Reject deleted or deactivated customer accounts.
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

        header('Location: /Nexora/View/Layout/routing.php?page=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $token = $_POST['csrf_token'] ?? '';

        if (
            !is_string($token) ||
            !hash_equals($_SESSION['account_csrf_token'], $token)
        ) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $firstNameInput = $_POST['first_name'] ?? '';
        $lastNameInput = $_POST['last_name'] ?? '';
        $emailInput = $_POST['email'] ?? '';
        $phoneInput = $_POST['phone'] ?? '';

        $firstName = is_string($firstNameInput)
            ? trim($firstNameInput)
            : '';

        $lastName = is_string($lastNameInput)
            ? trim($lastNameInput)
            : '';

        $email = is_string($emailInput)
            ? strtolower(trim($emailInput))
            : '';

        $phone = is_string($phoneInput)
            ? trim($phoneInput)
            : '';

        // Preserve submitted values if validation fails.
        $formData = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'phone'      => $phone
        ];

        if ($firstName === '' || mb_strlen($firstName) > 100) {
            $errors[] = 'Please enter a valid first name.';
        }

        if ($lastName === '' || mb_strlen($lastName) > 100) {
            $errors[] = 'Please enter a valid last name.';
        }

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 255
        ) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (strlen($phone) > 20) {
            $errors[] = 'Phone number must not exceed 20 characters.';
        }

        if (empty($errors)) {

            $existingCustomer = $customerModel->findByEmail($email);

            if (
                $existingCustomer &&
                (int) $existingCustomer['id'] !== $customerId
            ) {
                $errors[] = 'This email is already registered.';
            } else {

                $customerModel->updateCustomer(
                    $customerId,
                    $firstName,
                    $lastName,
                    $email,
                    $phone === '' ? null : $phone
                );

                $_SESSION['customer_name'] =
                    $firstName . ' ' . $lastName;

                $customer = $customerModel->getCustomerById($customerId);

                // Clear submitted values after successful update.
                $formData = [];

                $success = 'Profile updated successfully.';
            }
        }
    }

} catch (PDOException $e) {

    error_log($e->getMessage());

    if ($e->getCode() === '23000') {
        $errors[] = 'This email is already registered.';
    } else {
        $errors[] = 'Unable to update account information. Please try again later.';
    }
}
