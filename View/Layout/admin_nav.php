
<aside class="admin-sidebar">
  <a class="logo" href="../Layout/routing.php?page=admin-overview">
    <span class="logo-mark"></span>NEXORA
  </a>

  <span class="admin-label">OPERATIONS</span>

  <nav>
    <a class="<?= $currentPage === 'admin-overview' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-overview">
      ◫ <span>Overview</span>
    </a>

    <a class="<?= $currentPage === 'admin-products' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-products">
      ▦ <span>Products</span>
    </a>

    <a class="<?= $currentPage === 'admin-categories' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-categories">
      ◈ <span>Categories</span>
    </a>

    <a class="<?= $currentPage === 'admin-orders' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-orders">
      ⇄ <span>Orders</span>
    </a>

    <a class="<?= $currentPage === 'admin-customers' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-customers">
      ♙ <span>Customers</span>
    </a>

    <a class="<?= $currentPage === 'admin-coupons' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-coupons">
      ％ <span>Coupons &amp; sales</span>
    </a>
  </nav>

  <div class="sidebar-bottom">

    <a class="<?= $currentPage === 'admin-profile' ? 'active' : '' ?>"
       href="../Layout/routing.php?page=admin-profile">
      ⚙ <span>Admin profile</span>
    </a>

    <a href="../Layout/routing.php?page=home">
      ↗ <span>View storefront</span>
    </a>

    <form
      method="POST"
      action="/Nexora/Controllers/admin-logout.php"
      style="margin: 0;"
    >
      <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(
          $_SESSION['admin_csrf_token'] ?? '',
          ENT_QUOTES,
          'UTF-8'
        ) ?>"
      >

      <button
        type="submit"
        style="
          display: block;
          width: 100%;
          text-align: left;
          background: transparent;
          border: none;
          color: inherit;
          font: inherit;
          cursor: pointer;
          padding: 12px 16px;
        "
      >
        ⇥ <span>Sign out</span>
      </button>
    </form>

  </div>
</aside>
