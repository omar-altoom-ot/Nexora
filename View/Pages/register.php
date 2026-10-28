<?php
$currentPage = 'register';
$pageTitle = 'Create account — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <main class="auth-shell">
      <a class="logo" href="home.php"><span class="logo-mark"></span>NEXORA</a>
      <section class="auth-card">
        <span class="eyebrow">JOIN THE NEXT GENERATION</span>
        <h1>Create your <span class="text-grad">account.</span></h1>
        <p class="muted">Your smarter tech experience starts here.</p>
        <form>
          <div class="form-grid">
            <label class="field"
              ><span>First name</span
              ><input class="input" placeholder="Alex" required /></label
            ><label class="field"
              ><span>Last name</span
              ><input class="input" placeholder="Morgan" required
            /></label>
          </div>
          <label class="field"
            ><span>Email address</span
            ><input
              class="input"
              type="email"
              placeholder="you@example.com"
              required /></label
          ><label class="field"
            ><span>Phone <small>Optional</small></span
            ><input
              class="input"
              type="tel"
              placeholder="+962 7X XXX XXXX" /></label
          ><label class="field"
            ><span>Password</span
            ><input
              class="input"
              type="password"
              placeholder="At least 8 characters"
              required /></label
          ><button class="btn btn-primary full" type="submit">
            Create account
          </button>
        </form>
        <p class="auth-foot">
          Already registered? <a href="login.php">Sign in</a>
        </p>
      </section>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="home.php">Home</a><a href="products.php">Products</a
      ><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a
      ><a href="account.php">My account</a><a href="wishlist.php">Wishlist</a
      ><a href="login.php">Login</a><a href="register.php">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
