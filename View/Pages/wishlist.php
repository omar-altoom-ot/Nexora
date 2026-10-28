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
          <a class="p-img" href="../Layout/routing.php?page=product">🎮</a>
          <div class="p-body">
            <span class="p-cat">GRAPHICS CARDS</span
            ><a class="p-name" href="../Layout/routing.php?page=product">RTX 5080 16GB Gaming OC</a
            ><span class="p-stars">★★★★★</span>
            <div class="p-foot">
              <span class="p-price">1,199 <small>JD</small></span
              ><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login"
                >Add to cart</a
              >
            </div>
          </div>
        </article>
        <article class="card p-card">
          <a class="p-img" href="../Layout/routing.php?page=product">⌨️</a>
          <div class="p-body">
            <span class="p-cat">PERIPHERALS</span
            ><a class="p-name" href="../Layout/routing.php?page=product"
              >Mechanical RGB Keyboard 75%</a
            ><span class="p-stars">★★★★★</span>
            <div class="p-foot">
              <span class="p-price">89 <small>JD</small></span
              ><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login"
                >Add to cart</a
              >
            </div>
          </div>
        </article>
      </div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="../Layout/routing.php?page=home">Home</a><a href="../Layout/routing.php?page=products">Products</a
      ><a href="../Layout/routing.php?page=cart">Cart</a><a href="../Layout/routing.php?page=checkout">Checkout</a
      ><a href="../Layout/routing.php?page=account">My account</a><a href="../Layout/routing.php?page=wishlist">Wishlist</a
      ><a href="../Layout/routing.php?page=login">Login</a><a href="../Layout/routing.php?page=register">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

