<?php
$currentPage = 'wishlist';
$pageTitle = 'Wishlist — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main">
      <span class="eyebrow">// SAVED FOR LATER</span>
      <h1 class="page-title">Your <span class="text-grad">wishlist.</span></h1>
      <div class="grid grid-4 product-grid">
        <article class="card p-card">
          <a class="p-img" href="product.php">🎮</a>
          <div class="p-body">
            <span class="p-cat">GRAPHICS CARDS</span
            ><a class="p-name" href="product.php">RTX 5080 16GB Gaming OC</a
            ><span class="p-stars">★★★★★</span>
            <div class="p-foot">
              <span class="p-price">1,199 <small>JD</small></span
              ><a class="btn btn-primary btn-sm" href="login.php"
                >Add to cart</a
              >
            </div>
          </div>
        </article>
        <article class="card p-card">
          <a class="p-img" href="product.php">⌨️</a>
          <div class="p-body">
            <span class="p-cat">PERIPHERALS</span
            ><a class="p-name" href="product.php"
              >Mechanical RGB Keyboard 75%</a
            ><span class="p-stars">★★★★★</span>
            <div class="p-foot">
              <span class="p-price">89 <small>JD</small></span
              ><a class="btn btn-primary btn-sm" href="login.php"
                >Add to cart</a
              >
            </div>
          </div>
        </article>
      </div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="home.php">Home</a><a href="products.php">Products</a
      ><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a
      ><a href="account.php">My account</a><a href="wishlist.php">Wishlist</a
      ><a href="login.php">Login</a><a href="register.php">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
