<?php
$currentPage = 'admin-products';
$pageTitle = 'Products — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>PRODUCTS</span></small>
          <h2>Products</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="admin-profile.php">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// CATALOG CONTROL</span>
            <h1>Product <span class="text-grad">inventory.</span></h1>
            <p class="muted">
              Manage listings, stock levels, and catalog details.
            </p>
          </div>
          <button class="btn btn-primary btn-sm">＋ Add product</button>
        </div>
        <div class="grid grid-4 kpi-grid">
          <div class="kpi card"><small>TOTAL PRODUCTS</small><b>128</b></div>
          <div class="kpi card"><small>ACTIVE LISTINGS</small><b>116</b></div>
          <div class="kpi card">
            <small>LOW STOCK</small><b class="warn-text">07</b>
          </div>
          <div class="kpi card"><small>OUT OF STOCK</small><b>05</b></div>
        </div>
        <section class="card panel">
          <div class="table-toolbar">
            <input class="input" placeholder="Search products..." /><select
              class="input"
            >
              <option>All categories</option></select
            ><button class="btn btn-ghost btn-sm">Filter</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>PRODUCT</th>
                  <th>CATEGORY</th>
                  <th>PRICE</th>
                  <th>STOCK</th>
                  <th>STATUS</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>RTX 5080 16GB Gaming OC</td>
                  <td>Graphics cards</td>
                  <td>1,199 JD</td>
                  <td>14</td>
                  <td><span class="status status-green">Active</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
                <tr>
                  <td>Ryzen 9 9950X 16-Core</td>
                  <td>Processors</td>
                  <td>549 JD</td>
                  <td>08</td>
                  <td><span class="status status-green">Active</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
                <tr>
                  <td>Mechanical RGB Keyboard 75%</td>
                  <td>Peripherals</td>
                  <td>89 JD</td>
                  <td>03</td>
                  <td><span class="status status-amber">Low stock</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
                <tr>
                  <td>NVMe Gen5 SSD 2TB</td>
                  <td>Storage</td>
                  <td>169 JD</td>
                  <td>0</td>
                  <td><span class="status status-red">Out of stock</span></td>
                  <td><a href="#">Edit ·•••</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
