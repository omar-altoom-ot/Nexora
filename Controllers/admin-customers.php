
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../Model/Customer.php';

$customers = [];
$errors = [];

try {
    $db = new Db();

    $customerModel = new Customer($db);

    $customers = $customerModel->getAllCustomers();
} catch (PDOException $e) {

    error_log($e->getMessage());

    $errors[] = 'Unable to load customers. Please try again later.';
}
