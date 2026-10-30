<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['admin_id']) ||
    ($_SESSION['user_role'] ?? null) !== 'admin'
) {
    http_response_code(403);
    exit('Access denied.');
}
