<?php
$currentPage = 'account';
$pageTitle = 'My account — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main">
      <span class="eyebrow">// PLAYER PROFILE</span>
      <h1 class="page-title">Your <span class="text-grad">account.</span></h1>
      <div class="account-layout">
        <aside class="account-nav card">
          <div class="avatar">AM</div>
          <h3>Alex Morgan</h3>
          <small>MEMBER SINCE 2026</small
          ><a class="active" href="../Layout/routing.php?page=account">Profile overview</a
          ><a href="#orders">My orders</a><a href="../Layout/routing.php?page=wishlist">Wishlist</a
          ><a href="#settings">Account settings</a>
        </aside>
        <section>
          <div class="grid grid-3 account-stats">
            <div class="stat-card card"><small>ORDERS</small><b>08</b></div>
            <div class="stat-card card"><small>WISHLIST</small><b>12</b></div>
            <div class="stat-card card">
              <small>REWARDS</small><b>640 <small>XP</small></b>
            </div>
          </div>
          <article class="card profile-card">
            <div class="row-between">
              <div>
                <h2>Personal information</h2>
                <p class="muted">Manage your account details.</p>
              </div>
              <button class="btn btn-ghost btn-sm">Edit profile</button>
            </div>
            <div class="profile-grid">
              <div><small>FULL NAME</small><b>Alex Morgan</b></div>
              <div><small>EMAIL</small><b>alex.morgan@email.com</b></div>
              <div><small>PHONE</small><b>+962 7X XXX XXXX</b></div>
              <div><small>DEFAULT ADDRESS</small><b>Amman, Jordan</b></div>
            </div>
          </article>
          <article id="orders" class="card profile-card">
            <h2>Recent orders</h2>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>ORDER</th>
                    <th>DATE</th>
                    <th>ITEMS</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>#NX-2048</td>
                    <td>Oct 21, 2026</td>
                    <td>3 items</td>
                    <td>329 JD</td>
                    <td><span class="status status-green">Delivered</span></td>
                  </tr>
                  <tr>
                    <td>#NX-2031</td>
                    <td>Oct 12, 2026</td>
                    <td>1 item</td>
                    <td>89 JD</td>
                    <td><span class="status status-blue">Shipped</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </article>
        </section>
      </div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="../Layout/routing.php?page=home">Home</a><a href="../Layout/routing.php?page=products">Products</a
      ><a href="../Layout/routing.php?page=cart">Cart</a><a href="../Layout/routing.php?page=checkout">Checkout</a
      ><a href="../Layout/routing.php?page=account">My account</a><a href="../Layout/routing.php?page=wishlist">Wishlist</a
      ><a href="../Layout/routing.php?page=login">Login</a><a href="../Layout/routing.php?page=register">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

