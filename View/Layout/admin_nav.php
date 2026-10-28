<aside class="admin-sidebar">
  <a class="logo" href="../Layout/routing.php?page=admin-overview"><span class="logo-mark"></span>NEXORA</a>
  <span class="admin-label">OPERATIONS</span>
  <nav>
    <a class="<?= $currentPage === 'admin-overview' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-overview">◫ <span>Overview</span></a>
    <a class="<?= $currentPage === 'admin-products' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-products">▦ <span>Products</span></a>
    <a class="<?= $currentPage === 'admin-categories' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-categories">◈ <span>Categories</span></a>
    <a class="<?= $currentPage === 'admin-orders' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-orders">⇄ <span>Orders</span></a>
    <a class="<?= $currentPage === 'admin-customers' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-customers">♙ <span>Customers</span></a>
    <a class="<?= $currentPage === 'admin-coupons' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-coupons">％ <span>Coupons &amp; sales</span></a>
  </nav>
  <div class="sidebar-bottom">
    <a class="<?= $currentPage === 'admin-profile' ? 'active' : '' ?>" href="../Layout/routing.php?page=admin-profile">⚙ <span>Admin profile</span></a>
    <a href="../Layout/routing.php?page=home">↗ <span>View storefront</span></a>
    <a href="../Layout/routing.php?page=admin-login">⇥ <span>Sign out</span></a>
  </div>
</aside>

