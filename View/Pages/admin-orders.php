<?php
$currentPage = 'admin-orders';
$pageTitle = 'Orders — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>ORDERS</span></small>
          <h2>Orders</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// FULFILLMENT CENTER</span>
            <h1>Order <span class="text-grad">stream.</span></h1>
            <p class="muted">Track and manage every customer order.</p>
          </div>
          <button class="btn btn-ghost btn-sm">↓ Export orders</button>
        </div>
        <div class="order-tabs">
          <a class="tab-active" href="#">All orders <b>1,284</b></a
          ><a href="#">Pending <b>18</b></a
          ><a href="#">Processing <b>32</b></a
          ><a href="#">Shipped</a><a href="#">Delivered</a
          ><a href="#">Cancelled</a>
        </div>
        <section class="card panel">
          <div class="table-toolbar">
            <input
              class="input"
              placeholder="Search order or customer..."
            /><select class="input">
              <option>All statuses</option></select
            ><button class="btn btn-ghost btn-sm">Date range</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ORDER ID</th>
                  <th>CUSTOMER</th>
                  <th>DATE</th>
                  <th>ITEMS</th>
                  <th>TOTAL</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#NX-2084</td>
                  <td>Samir Haddad</td>
                  <td>Oct 28, 2026</td>
                  <td>3</td>
                  <td>1,288 JD</td>
                  <td><span class="status status-blue">Processing</span></td>
                  <td><a href="#">View →</a></td>
                </tr>
                <tr>
                  <td>#NX-2083</td>
                  <td>Lina Saleh</td>
                  <td>Oct 28, 2026</td>
                  <td>1</td>
                  <td>349 JD</td>
                  <td><span class="status status-green">Delivered</span></td>
                  <td><a href="#">View →</a></td>
                </tr>
                <tr>
                  <td>#NX-2082</td>
                  <td>Omar Nasser</td>
                  <td>Oct 27, 2026</td>
                  <td>2</td>
                  <td>89 JD</td>
                  <td><span class="status status-amber">Pending</span></td>
                  <td><a href="#">View →</a></td>
                </tr>
                <tr>
                  <td>#NX-2081</td>
                  <td>Maya Khalil</td>
                  <td>Oct 27, 2026</td>
                  <td>1</td>
                  <td>729 JD</td>
                  <td><span class="status status-pink">Shipped</span></td>
                  <td><a href="#">View →</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

