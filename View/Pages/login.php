<?php
$currentPage = 'login';
$pageTitle = 'Sign in — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <main class="auth-shell">
      <a class="logo" href="home.php"><span class="logo-mark"></span>NEXORA</a>
      <section class="auth-card">
        <span class="eyebrow">PLAYER ACCESS // 01</span>
        <h1>Welcome <span class="text-grad">back.</span></h1>
        <p class="muted">Sign in to continue your next build.</p>
        <form>
          <label class="field"
            ><span>Email address</span
            ><input
              class="input"
              type="email"
              placeholder="you@example.com"
              required /></label
          ><label class="field"
            ><span>Password</span
            ><input
              class="input"
              type="password"
              placeholder="••••••••"
              required
          /></label>
          <div class="row-between">
            <label class="check"><input type="checkbox" /> Remember me</label
            ><a href="#">Forgot password?</a>
          </div>
          <button class="btn btn-primary full" type="submit">
            Enter NEXORA
          </button>
        </form>
        <p class="auth-foot">
          New to the arena? <a href="register.php">Create account</a>
        </p>
        <a class="back-link" href="home.php">← Back to store</a>
      </section>
      <div class="auth-orbit"></div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="home.php">Home</a><a href="products.php">Products</a
      ><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a
      ><a href="account.php">My account</a><a href="wishlist.php">Wishlist</a
      ><a href="login.php">Login</a><a href="register.php">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
