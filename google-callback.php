<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/Config/DBconnection.php';
$config = require __DIR__ . '/Config/google-oauth.php';
$loginUrl = '/Nexora/View/Layout/routing.php?page=login';
$fail = static function (string $reason) use ($loginUrl): void {
    unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_verifier']);
    header('Location: ' . $loginUrl . '&google_error=' . rawurlencode($reason));
    exit;
};

if (isset($_GET['error'])) $fail('cancelled');
$returnedState = $_GET['state'] ?? '';
$expectedState = $_SESSION['google_oauth_state'] ?? '';
$code = $_GET['code'] ?? '';
$verifier = $_SESSION['google_oauth_verifier'] ?? '';
if (!is_string($returnedState) || !is_string($expectedState) || $expectedState === '' || !hash_equals($expectedState, $returnedState)
    || !is_string($code) || $code === '' || !is_string($verifier) || $verifier === '') {
    $fail('invalid_response');
}
unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_verifier']);

if ($config['client_id'] === '' || $config['client_secret'] === '' || !function_exists('curl_init')) {
    $fail('not_configured');
}

$postForm = static function (string $url, array $fields): array|false {
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
        CURLOPT_TIMEOUT => 15,
    ]);
    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if (!is_string($body) || $status < 200 || $status >= 300) return false;
    $json = json_decode($body, true);
    return is_array($json) ? $json : false;
};

$tokens = $postForm('https://oauth2.googleapis.com/token', [
    'code' => $code,
    'client_id' => $config['client_id'],
    'client_secret' => $config['client_secret'],
    'redirect_uri' => $config['redirect_uri'],
    'grant_type' => 'authorization_code',
    'code_verifier' => $verifier,
]);
if (!is_array($tokens) || empty($tokens['access_token'])) $fail('token_exchange');

$curl = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokens['access_token'], 'Accept: application/json'],
    CURLOPT_TIMEOUT => 15,
]);
$body = curl_exec($curl);
$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$profile = is_string($body) && $status >= 200 && $status < 300 ? json_decode($body, true) : null;
$emailVerified = is_array($profile) && (($profile['email_verified'] ?? false) === true || ($profile['email_verified'] ?? null) === 'true');
if (!is_array($profile) || empty($profile['sub']) || empty($profile['email']) || !$emailVerified) {
    $fail('profile');
}

$googleId = (string) $profile['sub'];
$email = strtolower(trim((string) $profile['email']));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $fail('profile');

try {
    $db = new Db();
    $customer = $db->run_query('SELECT * FROM customers WHERE google_id = ? LIMIT 1', [$googleId])->fetch();
    if (!$customer) {
        $customer = $db->run_query('SELECT * FROM customers WHERE email = ? LIMIT 1', [$email])->fetch();
        if ($customer) {
            if (!empty($customer['google_id']) && !hash_equals((string) $customer['google_id'], $googleId)) {
                $fail('email_conflict');
            }
            $db->run_query('UPDATE customers SET google_id = ? WHERE id = ? AND google_id IS NULL', [$googleId, $customer['id']]);
            $customer = $db->run_query('SELECT * FROM customers WHERE id = ? LIMIT 1', [$customer['id']])->fetch();
        } else {
            $givenName = trim((string) ($profile['given_name'] ?? ''));
            $familyName = trim((string) ($profile['family_name'] ?? ''));
            if ($givenName === '') {
                $parts = preg_split('/\s+/', trim((string) ($profile['name'] ?? '')));
                $givenName = (string) array_shift($parts);
                if ($familyName === '' && $parts) $familyName = implode(' ', $parts);
            }
            if ($givenName === '') $givenName = strstr($email, '@', true) ?: 'Google';
            if ($familyName === '') $familyName = 'User';
            $db->run_query(
                'INSERT INTO customers (first_name, last_name, email, password_hash, google_id) VALUES (?, ?, ?, NULL, ?)',
                [$givenName, $familyName, $email, $googleId]
            );
            $customer = $db->run_query('SELECT * FROM customers WHERE google_id = ? LIMIT 1', [$googleId])->fetch();
        }
    }
} catch (PDOException $e) {
    error_log('Google sign-in database error: ' . $e->getMessage());
    $fail($e->getCode() === '23000' ? 'email_conflict' : 'server');
}

if (!$customer || (int) ($customer['is_active'] ?? 0) !== 1) $fail('inactive');
session_regenerate_id(true);
$_SESSION['customer_id'] = (int) $customer['id'];
$_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];
$_SESSION['user_role'] = 'customer';
$postLoginPage = $_SESSION['post_login_page'] ?? 'home';
unset($_SESSION['post_login_page']);
if (!in_array($postLoginPage, ['home', 'account', 'cart'], true)) $postLoginPage = 'home';
header('Location: /Nexora/View/Layout/routing.php?page=' . $postLoginPage);
exit;
