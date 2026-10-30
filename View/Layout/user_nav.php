
<?php
// Abdullah ALqudah

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedInCustomer =
    isset($_SESSION['customer_id']) &&
    ($_SESSION['user_role'] ?? null) === 'customer';

if ($isLoggedInCustomer && empty($_SESSION['customer_logout_csrf_token'])) {
    $_SESSION['customer_logout_csrf_token'] = bin2hex(random_bytes(32));
}
?>

<header class="nav">
  <div class="container nav-inner">

    <a class="logo" href="../Layout/routing.php?page=home">
      <span class="logo-mark"></span>NEXORA
    </a>

    <nav class="nav-links">
      <a class="<?= $currentPage === 'home' ? 'active' : '' ?>"
         href="../Layout/routing.php?page=home">
        Home
      </a>

      <a class="<?= in_array($currentPage, ['products', 'product'], true) ? 'active' : '' ?>"
         href="../Layout/routing.php?page=products">
        Products
      </a>

      <!-- <a href="#">PC Builder</a> -->
	   <a class="<?= $currentPage === 'builder' ? 'active' : '' ?>" href="../Layout/routing.php?page=builder">PC Builder</a>
      <a href="#">Compare</a>
    </nav>

    <div class="nav-actions">

      <a class="icon-btn"
         href="../Layout/routing.php?page=wishlist"
         aria-label="Wishlist">
        ♡
      </a>

      <a class="icon-btn"
         href="../Layout/routing.php?page=cart"
         aria-label="Cart">
        🛒
      </a>

      <?php if ($isLoggedInCustomer): ?>

        <a class="btn btn-primary btn-sm"
           href="../Layout/routing.php?page=account">
          My Account
        </a>

        <form
          action="/Nexora/Controllers/customer-logout.php"
          method="POST"
          style="display:inline; margin:0;"
        >
          <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['customer_logout_csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
          >

          <button
            type="submit"
            class="btn btn-primary btn-sm">
            Logout
          </button>
        </form>

      <?php else: ?>

        <a class="btn btn-primary btn-sm"
           href="../Layout/routing.php?page=login">
          Login
        </a>

      <?php endif; ?>

    </div>
  </div>
</header>
