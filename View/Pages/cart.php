<?php
$currentPage = 'cart';
$pageTitle = 'Your cart — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main">
      <span class="eyebrow">// YOUR LOADOUT</span>
      <h1 class="page-title">Shopping <span class="text-grad">cart.</span></h1>
      <div class="cart-layout">
        <section class="cart-items">
          <article class="cart-item card">
            <div class="mini-visual">🎮</div>
            <div class="item-copy">
              <small>GRAPHICS CARDS</small>
              <h3>RTX 5080 16GB Gaming OC</h3>
              <span>In stock · SKU NX-5080-16</span><a href="#">Remove</a>
            </div>
            <div class="quantity">
              <button>−</button><b>1</b><button>+</button>
            </div>
            <b class="item-price">1,199 JD</b>
          </article>
          <article class="cart-item card">
            <div class="mini-visual">⌨️</div>
            <div class="item-copy">
              <small>PERIPHERALS</small>
              <h3>Mechanical RGB Keyboard 75%</h3>
              <span>In stock · SKU NX-KB-075</span><a href="#">Remove</a>
            </div>
            <div class="quantity">
              <button>−</button><b>1</b><button>+</button>
            </div>
            <b class="item-price">89 JD</b>
          </article>
          <a href="products.php" class="back-link">← Continue shopping</a>
        </section>
        <aside class="summary card">
          <h2>Order summary</h2>
          <div class="coupon">
            <input class="input" placeholder="Coupon code" /><button
              class="btn btn-ghost"
            >
              Apply
            </button>
          </div>
          <div class="summary-line">
            <span>Subtotal</span><b>1,288.00 JD</b>
          </div>
          <div class="summary-line">
            <span>Estimated tax (5%)</span><b>64.40 JD</b>
          </div>
          <div class="summary-line"><span>Delivery</span><b>2.00 JD</b></div>
          <div class="summary-total"><span>Total</span><b>1,354.40 JD</b></div>
          <a class="btn btn-primary full" href="checkout.php"
            >Continue to checkout</a
          ><a class="cancel-link" href="products.php">Cancel order</a
          ><small class="secure"
            >◈ Secure checkout · Tax calculated at 5%</small
          >
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
