<?php
// Qudah edit

require_once __DIR__ . '/../../Controllers/register.php';

$currentPage = 'register';
$pageTitle = 'Create account — NEXORA';
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

    <span class="eyebrow">JOIN THE NEXT GENERATION</span>

    <h1>
      Create your <span class="text-grad">account.</span>
    </h1>

    <p class="muted">
      Your smarter tech experience starts here.
    </p>

    <form method="POST" action="/Nexora/View/Layout/routing.php?page=register">
      <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

      <?php foreach ($errors as $error): ?>
        <p style="color: #ef4444;">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endforeach; ?>

      <?php if ($success !== ''): ?>
        <p style="color: #22c55e;">
          <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>

      <div class="form-grid">

        <label class="field">
          <span>First name</span>
          <input
            class="input"
            type="text"
            name="first_name"
            placeholder="Alex"
            value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>"
            required />
        </label>

        <label class="field">
          <span>Last name</span>
          <input
            class="input"
            type="text"
            name="last_name"
            placeholder="Morgan"
            value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8') ?>"
            required />
        </label>

      </div>

      <label class="field">
        <span>Email address</span>
        <input
          class="input"
          type="email"
          name="email"
          placeholder="you@example.com"
          value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
          required />
      </label>

      <label class="field">
        <span>Phone <small>Optional</small></span>
        <input
          class="input"
          type="tel"
          name="phone"
          placeholder="+962 7X XXX XXXX"
          value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>" />
      </label>

      <label class="field">
        <span>Password</span>
        <input
          class="input"
          type="password"
          name="password"
          placeholder="At least 8 characters"
          minlength="8"
          required />
      </label>

      <button class="btn btn-primary full" type="submit">
        Create account
      </button>

    </form>

    <p class="auth-foot">
      Already registered?
      <a href="../Layout/routing.php?page=login">Sign in</a>
    </p>

  </section>

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