<?php

$localConfig = __DIR__ . '/google-oauth.local.php';
$config = is_file($localConfig) ? require $localConfig : [];

return [
    'client_id' => trim((string) ($config['client_id'] ?? '')),
    'client_secret' => trim((string) ($config['client_secret'] ?? '')),
    'redirect_uri' => trim((string) ($config['redirect_uri'] ?? 'http://localhost/Nexora/google-callback.php')),
];
