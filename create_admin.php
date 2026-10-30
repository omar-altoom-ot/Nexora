<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/Config/DBconnection.php';

$db = new Db();

$name = 'Admin';
$email = 'admin@nexora.com';
$password = 'Admin12345!';

$stmt = $db->run_query(
    "SELECT id FROM admins WHERE email = ?",
    [$email]
);

if ($stmt->fetch()) {
    exit("Admin already exists!");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$db->run_query(
    "INSERT INTO admins (name, email, password_hash)
     VALUES (?, ?, ?)",
    [$name, $email, $passwordHash]
);

echo "Admin created successfully!";