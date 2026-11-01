<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$config = require __DIR__ . '/Config/google-oauth.php';
$loginUrl = '/Nexora/View/Layout/routing.php?page=login';
if ($config['client_id'] === '' || $config['client_secret'] === '' || str_contains($config['client_id'], 'YOUR_GOOGLE_')) {
    header('Location: ' . $loginUrl . '&google_error=not_configured');
    exit;
}

$state = bin2hex(random_bytes(32));
$verifier = rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
$_SESSION['google_oauth_state'] = $state;
$_SESSION['google_oauth_verifier'] = $verifier;

$challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
$query = http_build_query([
    'client_id' => $config['client_id'],
    'redirect_uri' => $config['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state,
    'code_challenge' => $challenge,
    'code_challenge_method' => 'S256',
    'prompt' => 'select_account',
]);
header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $query);
exit;
