<?php
$errorCode = http_response_code();
$errorCode = is_int($errorCode) && $errorCode >= 400 ? $errorCode : 500;
$errorTitle = match ($errorCode) {
    400 => 'Invalid request',
    403 => 'Access denied',
    404 => 'Page not found',
    default => 'Something went wrong',
};
$errorMessage = $_SESSION['error_custom_message'] ?? match ($errorCode) {
    400 => 'The request could not be understood.',
    403 => 'You do not have permission to view this page.',
    404 => 'The page you are looking for does not exist.',
    default => 'We could not complete your request. Please try again.',
};
unset($_SESSION['error_custom_message']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') ?> — NEXORA</title>
  <link rel="stylesheet" href="../css/theme.css">
  <style>
    body{min-height:100vh;display:grid;place-items:center;padding:24px}
    .error-card{width:min(560px,100%);padding:clamp(26px,6vw,48px);text-align:center;border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(21,16,48,.96),rgba(10,7,22,.98));box-shadow:var(--glow)}
    .error-code{font:900 clamp(3.5rem,14vw,6rem)/1 var(--font-head);letter-spacing:.05em;background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent}
    .error-card h1{margin:16px 0 10px;font:700 clamp(1.3rem,5vw,1.8rem) var(--font-head)}
    .error-card p{color:var(--muted);margin:0 auto 26px;max-width:430px}
    .error-actions{display:flex;justify-content:center;gap:10px;flex-wrap:wrap}
  </style>
</head>
<body>
  <main class="error-card">
    <div class="error-code"><?= (int) $errorCode ?></div>
    <h1><?= htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
    <div class="error-actions">
      <a class="btn btn-primary" href="../Layout/routing.php?page=home">Back to home</a>
      <?php if ($errorCode === 403): ?><a class="btn btn-ghost" href="../Layout/routing.php?page=login">Customer login</a><?php endif; ?>
    </div>
  </main>
</body>
</html>
