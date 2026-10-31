
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/Customer.php';

// Only admins can manage customers
if (
    !isset($_SESSION['admin_id']) ||
    ($_SESSION['user_role'] ?? null) !== 'admin'
) {
    header('Location: /Nexora/View/Layout/routing.php?page=admin-login');
    exit;
}

$customers = [];
$errors = [];
$success = '';

// Generate CSRF token
if (empty($_SESSION['admin_customers_csrf_token'])) {
    $_SESSION['admin_customers_csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $db = new Db();
    $customerModel = new Customer($db);

    // Handle status changes
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $token = $_POST['csrf_token'] ?? '';

        if (
            !is_string($token) ||
            !hash_equals($_SESSION['admin_customers_csrf_token'], $token)
        ) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $customerId = filter_var(
            $_POST['customer_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $action = $_POST['action'] ?? '';

        if (!$customerId || !is_string($action)) {
            $errors[] = 'Invalid customer request.';
        } elseif ($action === 'deactivate') {

            $updated = $customerModel->deactivateCustomer($customerId);

            if ($updated) {
                $success = 'Customer account deactivated successfully.';
            } else {
                $errors[] = 'Customer could not be deactivated.';
            }

        } elseif ($action === 'reactivate') {

            $updated = $customerModel->reactivateCustomer($customerId);

            if ($updated) {
                $success = 'Customer account reactivated successfully.';
            } else {
                $errors[] = 'Customer could not be reactivated.';
            }

        } else {
            $errors[] = 'Invalid action.';
        }
    }

    // Reload customers after changes
    $customers = $customerModel->getAllCustomers();

} catch (PDOException $e) {

    error_log($e->getMessage());

    $errors[] = 'Unable to manage customers. Please try again later.';
}
