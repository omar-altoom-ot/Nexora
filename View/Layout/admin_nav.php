<aside class="admin-sidebar">
  <a class="logo" href="admin-overview.php"><span class="logo-mark"></span>NEXORA</a>
  <span class="admin-label">OPERATIONS</span>
  <nav>
    <a class="<?= $currentPage === 'admin-overview' ? 'active' : '' ?>" href="admin-overview.php">◫ <span>Overview</span></a>
    <a class="<?= $currentPage === 'admin-products' ? 'active' : '' ?>" href="admin-products.php">▦ <span>Products</span></a>
    <a class="<?= $currentPage === 'admin-categories' ? 'active' : '' ?>" href="admin-categories.php">◈ <span>Categories</span></a>
    <a class="<?= $currentPage === 'admin-orders' ? 'active' : '' ?>" href="admin-orders.php">⇄ <span>Orders</span></a>
    <a class="<?= $currentPage === 'admin-customers' ? 'active' : '' ?>" href="admin-customers.php">♙ <span>Customers</span></a>
    <a class="<?= $currentPage === 'admin-coupons' ? 'active' : '' ?>" href="admin-coupons.php">％ <span>Coupons &amp; sales</span></a>
  </nav>
  <div class="sidebar-bottom">
    <a class="<?= $currentPage === 'admin-profile' ? 'active' : '' ?>" href="admin-profile.php">⚙ <span>Admin profile</span></a>
    <a href="home.php">↗ <span>View storefront</span></a>
    <a href="admin-login.php">⇥ <span>Sign out</span></a>
  </div>
</aside>
