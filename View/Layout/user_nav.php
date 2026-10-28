<header class="nav">
  <div class="container nav-inner">
    <a class="logo" href="../Layout/routing.php?page=home"><span class="logo-mark"></span>NEXORA</a>
    <nav class="nav-links">
      <a class="<?= $currentPage === 'home' ? 'active' : '' ?>" href="../Layout/routing.php?page=home">Home</a>
      <a class="<?= in_array($currentPage, ['products', 'product'], true) ? 'active' : '' ?>" href="../Layout/routing.php?page=products">Products</a>
      <a href="#">PC Builder</a><a href="#">Compare</a>
    </nav>
    <div class="nav-actions">
      <a class="icon-btn" href="../Layout/routing.php?page=wishlist" aria-label="Wishlist">♡</a>
      <a class="icon-btn" href="../Layout/routing.php?page=cart" aria-label="Cart">🛒</a>
      <a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login">Login</a>
    </div>
  </div>
</header>

