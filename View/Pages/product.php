<?php
$currentPage = 'product';
$pageTitle = 'RTX 5080 Gaming OC — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main">
      <p class="breadcrumb">
        <a href="home.php">Home</a> /
        <a href="products.php">Graphics cards</a> / RTX 5080
      </p>
      <div class="product-detail">
        <div class="gallery">
          <div class="product-visual">
            🎮<span class="badge badge-ok">IN STOCK</span
            ><span class="visual-caption">NEXORA / PRODUCT VISUAL</span>
          </div>
          <div class="thumbs">
            <button>🎮</button><button>⚡</button><button>◈</button>
          </div>
        </div>
        <section class="product-info">
          <span class="eyebrow">GRAPHICS CARD · NVIDIA</span>
          <h1>RTX 5080 <span class="text-grad">16GB</span> Gaming OC</h1>
          <div class="rating">★★★★★ <span>4.9 · 38 reviews</span></div>
          <p class="muted">
            Built for uncompromising 4K gaming and next-gen creative workloads.
            Advanced cooling, premium power delivery, and AI-accelerated
            performance.
          </p>
          <div class="detail-price">
            1,199 <small>JD</small
            ><span class="stock">● In stock · ships in 1–2 days</span>
          </div>
          <div class="spec-strip">
            <div><small>MEMORY</small><b>16 GB GDDR7</b></div>
            <div><small>INTERFACE</small><b>PCIe 5.0</b></div>
            <div><small>WARRANTY</small><b>2 years</b></div>
          </div>
          <div class="purchase-row">
            <div class="quantity">
              <button>−</button><b>1</b><button>+</button>
            </div>
            <a class="btn btn-primary" href="login.php"
              >Add to cart · Sign in</a
            ><a
              class="icon-btn wishlist"
              href="login.php"
              aria-label="Add to wishlist"
              >♡</a
            >
          </div>
          <div class="compat">
            <span>✦</span>
            <div>
              <b>AI compatibility insight</b>
              <p>
                Great match for high-end gaming builds. Check your full setup
                with PC Builder.
              </p>
            </div>
          </div>
          <div class="tabs">
            <a class="tab-active" href="#description">Description</a
            ><a href="#specs">Specifications</a
            ><a href="#reviews">Reviews (38)</a>
          </div>
          <div id="description" class="description">
            <h3>Performance without limits</h3>
            <p>
              Engineered for high frame rates, detailed worlds, and demanding
              creative workflows. Pair with a modern power supply and a spacious
              case for best results.
            </p>
          </div>
        </section>
      </div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="home.php">Home</a><a href="products.php">Products</a
      ><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a
      ><a href="account.php">My account</a><a href="wishlist.php">Wishlist</a
      ><a href="login.php">Login</a><a href="register.php">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
