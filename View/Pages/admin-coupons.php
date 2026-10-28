<?php
$currentPage = 'admin-coupons';
$pageTitle = 'Coupons &amp; sales — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>COUPONS &amp; SALES</span></small>
          <h2>Coupons &amp; sales</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// GROWTH ENGINE</span>
            <h1>Coupons <span class="text-grad">&amp; sales.</span></h1>
            <p class="muted">
              Create focused offers and track their performance.
            </p>
          </div>
          <button class="btn btn-primary btn-sm">＋ Create coupon</button>
        </div>
        <div class="grid grid-3 kpi-grid">
          <div class="kpi card"><small>ACTIVE COUPONS</small><b>06</b></div>
          <div class="kpi card">
            <small>REDEMPTIONS THIS MONTH</small><b>384</b>
          </div>
          <div class="kpi card">
            <small>DISCOUNT GIVEN</small><b>2,840 JD</b>
          </div>
        </div>
        <section class="card panel">
          <div class="row-between">
            <div>
              <h3>Active campaigns</h3>
              <p class="muted">Offer codes and usage</p>
            </div>
            <a href="#">View expired →</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>CODE</th>
                  <th>OFFER</th>
                  <th>VALID UNTIL</th>
                  <th>USAGE</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><b>LEVELUP15</b></td>
                  <td>15% off gaming gear</td>
                  <td>Nov 15, 2026</td>
                  <td>84 / 200</td>
                  <td><span class="status status-green">Active</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
                <tr>
                  <td><b>BUILD50</b></td>
                  <td>50 JD off PC components</td>
                  <td>Nov 30, 2026</td>
                  <td>42 / 100</td>
                  <td><span class="status status-green">Active</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
                <tr>
                  <td><b>WELCOME10</b></td>
                  <td>10% first order</td>
                  <td>Dec 31, 2026</td>
                  <td>258 / 500</td>
                  <td><span class="status status-green">Active</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
        <section class="card panel">
          <h3>Featured sale</h3>
          <p class="muted">
            Weekend peripherals drop · 10% off selected keyboards and mice
          </p>
          <div class="sale-band">
            <b>LEVEL UP YOUR SETUP</b><span>10% OFF · ENDS SUNDAY</span>
          </div>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

