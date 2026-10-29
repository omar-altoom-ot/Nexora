
<?php

require_once __DIR__ . '/Config/DBconnection.php';

// Temporary local setup script.
// Delete this file immediately after creating the admin.

$name = 'NEXORA Admin';
$email = 'admin@nexora.tech';
$password = 'Admin@12345';

try {
    $db = new Db();

    $stmt = $db->run_query(
        "SELECT id FROM admins WHERE email = ? LIMIT 1",
        [$email]
    );

    if ($stmt->fetch()) {
        exit('Admin account already exists.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $db->run_query(
        "INSERT INTO admins (name, email, password_hash)
         VALUES (?, ?, ?)",
        [$name, $email, $passwordHash]
    );

    echo 'Admin created successfully.';

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo 'Unable to create admin.';
}
