<header class="nav">
  <div class="container nav-inner">
    <a class="logo" href="home.php"><span class="logo-mark"></span>NEXORA</a>
    <nav class="nav-links">
      <a class="<?= $currentPage === 'home' ? 'active' : '' ?>" href="home.php">Home</a>
      <a class="<?= in_array($currentPage, ['products', 'product'], true) ? 'active' : '' ?>" href="products.php">Products</a>
      <a href="#">PC Builder</a><a href="#">Compare</a>
    </nav>
    <div class="nav-actions">
      <a class="icon-btn" href="wishlist.php" aria-label="Wishlist">♡</a>
      <a class="icon-btn" href="cart.php" aria-label="Cart">🛒</a>
      <a class="btn btn-primary btn-sm" href="login.php">Login</a>
    </div>
  </div>
</header>
