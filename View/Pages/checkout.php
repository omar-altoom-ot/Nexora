<?php
$currentPage = 'checkout';
$pageTitle = 'Checkout — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main narrow">
      <span class="eyebrow">// FINAL CHECKPOINT</span>
      <h1 class="page-title">
        Confirm your <span class="text-grad">order.</span>
      </h1>
      <div class="checkout-layout">
        <form class="card checkout-form">
          <h2>Delivery details</h2>
          <p class="muted">Where should we deliver your gear?</p>
          <label class="field"
            ><span>Full name</span
            ><input
              class="input"
              placeholder="Your full name"
              required /></label
          ><label class="field"
            ><span>Delivery address</span
            ><textarea
              class="input"
              rows="3"
              placeholder="Street, building, area, city"
              required
            ></textarea></label
          ><label class="field"
            ><span>Phone number</span
            ><input
              class="input"
              type="tel"
              placeholder="+962 7X XXX XXXX"
              required /></label
          ><label class="field"
            ><span>Order notes <small>Optional</small></span
            ><input class="input" placeholder="Delivery instructions"
          /></label>
          <div class="notice">
            Cash on delivery · Payment method can be selected at confirmation.
          </div>
          <button class="btn btn-primary full" type="submit">
            Confirm order · 1,354.40 JD
          </button>
        </form>
        <aside class="summary card">
          <h2>Order details</h2>
          <div class="summary-line"><span>2 items</span><b>1,288.00 JD</b></div>
          <div class="summary-line"><span>Tax · 5%</span><b>64.40 JD</b></div>
          <div class="summary-line"><span>Delivery</span><b>2.00 JD</b></div>
          <div class="summary-total">
            <span>Total due</span><b>1,354.40 JD</b>
          </div>
          <small class="secure">Final amount includes tax and delivery.</small>
        </aside>
      </div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="home.php">Home</a><a href="products.php">Products</a
      ><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a
      ><a href="account.php">My account</a><a href="wishlist.php">Wishlist</a
      ><a href="login.php">Login</a><a href="register.php">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
