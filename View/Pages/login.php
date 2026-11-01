
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../../Controllers/login.php';

$googleErrors = [
  'not_configured' => 'Google sign-in is not configured yet. Add your OAuth credentials in Config/google-oauth.local.php.',
  'cancelled' => 'Google sign-in was cancelled.',
  'invalid_response' => 'Google sign-in could not be verified. Please try again.',
  'token_exchange' => 'Could not complete sign-in with Google. Please try again.',
  'profile' => 'Google did not return a verified email address.',
  'email_conflict' => 'This email is connected to a different sign-in method. Sign in with your password first.',
  'inactive' => 'Your account has been deactivated. Please contact support.',
  'server' => 'Unable to sign in with Google right now. Please try again later.',
];
if (isset($_GET['google_error']) && is_string($_GET['google_error']) && isset($googleErrors[$_GET['google_error']])) {
  $errors[] = $googleErrors[$_GET['google_error']];
}

$currentPage = 'login';
$pageTitle = 'Sign in — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../Includes/header.php';
?>

<style>
.auth-divider{display:flex;align-items:center;gap:14px;margin:20px 0;color:var(--muted);font-size:.75rem;letter-spacing:.12em}
.auth-divider::before,.auth-divider::after{content:"";height:1px;flex:1;background:var(--line)}
.google-sign-in{display:flex;align-items:center;justify-content:center;gap:12px;text-decoration:none}
.google-sign-in svg{flex:none}
</style>

<main class="auth-shell">

  <a class="logo" href="../Layout/routing.php?page=home">
    <span class="logo-mark"></span>NEXORA
  </a>

  <section class="auth-card">

    <span class="eyebrow">PLAYER ACCESS // 01</span>

    <h1>Welcome <span class="text-grad">back.</span></h1>

    <p class="muted">Sign in to continue your next build.</p>

    <?php foreach ($errors as $error): ?>
      <p style="color: #ef4444;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endforeach; ?>

    <form
      method="POST"
      action="/Nexora/View/Layout/routing.php?page=login"
    >

      <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(
          $_SESSION['customer_csrf_token'],
          ENT_QUOTES,
          'UTF-8'
        ) ?>"
      >

      <label class="field">
        <span>Email address</span>

        <input
          class="input"
          type="email"
          name="email"
          placeholder="you@example.com"
          value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
          autocomplete="email"
          required
        >
      </label>

      <label class="field">
        <span>Password</span>

        <input
          class="input"
          type="password"
          name="password"
          placeholder="••••••••"
          autocomplete="current-password"
          required
        >
      </label>

      <div class="row-between">

        <label class="check">
          <input type="checkbox" name="remember_me" value="1">
          Remember me
        </label>

        <a href="#">Forgot password?</a>

      </div>

      <button class="btn btn-primary full" type="submit">
        Enter NEXORA
      </button>

    </form>

    <div class="auth-divider" role="separator"><span>OR</span></div>
    <a class="btn btn-ghost full google-sign-in" href="/Nexora/google-login.php">
      <svg aria-hidden="true" viewBox="0 0 48 48" width="20" height="20"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 3.07 13.22l7.98 6.19C12.94 13.72 18.03 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.72 7.18l7.62 5.91c4.45-4.11 7.14-10.16 7.14-17.56z"/><path fill="#FBBC05" d="M11.05 28.59A14.5 14.5 0 0 1 10.29 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.9 23.9 0 0 0 0 24c0 3.88.93 7.55 3.07 10.78l7.98-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.9-5.79l-7.62-5.91c-2.11 1.41-4.81 2.25-8.28 2.25-5.97 0-11.06-4.22-12.95-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
      Continue with Google
    </a>

    <p class="auth-foot">
      New to the arena?
      <a href="../Layout/routing.php?page=register">
        Create account
      </a>
    </p>

    <a class="back-link" href="../Layout/routing.php?page=home">
      ← Back to store
    </a>

  </section>

  <div class="auth-orbit"></div>

</main>

<nav class="page-links" aria-label="Store pages">
  <a href="../Layout/routing.php?page=home">Home</a>
  <a href="../Layout/routing.php?page=products">Products</a>
  <a href="../Layout/routing.php?page=cart">Cart</a>
  <a href="../Layout/routing.php?page=checkout">Checkout</a>
  <a href="../Layout/routing.php?page=account">My account</a>
  <a href="../Layout/routing.php?page=wishlist">Wishlist</a>
  <a href="../Layout/routing.php?page=login">Login</a>
  <a href="../Layout/routing.php?page=register">Register</a>
</nav>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
