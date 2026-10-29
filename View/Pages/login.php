
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../../Controllers/login.php';

$currentPage = 'login';
$pageTitle = 'Sign in — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../Includes/header.php';
?>

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
