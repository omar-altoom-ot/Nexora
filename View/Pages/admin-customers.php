<?php
$currentPage = 'admin-customers';
$pageTitle = 'Customers — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>CUSTOMERS</span></small>
          <h2>Customers</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// COMMUNITY INTEL</span>
            <h1>Customer <span class="text-grad">base.</span></h1>
            <p class="muted">Understand and support your NEXORA community.</p>
          </div>
          <button class="btn btn-ghost btn-sm">↓ Export list</button>
        </div>
        <div class="grid grid-3 kpi-grid">
          <div class="kpi card"><small>TOTAL CUSTOMERS</small><b>8,642</b></div>
          <div class="kpi card"><small>NEW THIS MONTH</small><b>426</b></div>
          <div class="kpi card">
            <small>RETURNING CUSTOMERS</small><b>64%</b>
          </div>
        </div>
        <section class="card panel">
          <div class="table-toolbar">
            <input class="input" placeholder="Search name or email..." /><select
              class="input"
            >
              <option>All customers</option>
            </select>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>CUSTOMER</th>
                  <th>EMAIL</th>
                  <th>PHONE</th>
                  <th>ORDERS</th>
                  <th>LIFETIME VALUE</th>
                  <th>JOINED</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Samir Haddad</td>
                  <td>samir.h@email.com</td>
                  <td>+962 79 ••• ••41</td>
                  <td>12</td>
                  <td>2,480 JD</td>
                  <td>May 12, 2026</td>
                </tr>
                <tr>
                  <td>Lina Saleh</td>
                  <td>lina.s@email.com</td>
                  <td>+962 78 ••• ••12</td>
                  <td>08</td>
                  <td>1,940 JD</td>
                  <td>Jun 04, 2026</td>
                </tr>
                <tr>
                  <td>Omar Nasser</td>
                  <td>omar.n@email.com</td>
                  <td>+962 77 ••• ••90</td>
                  <td>05</td>
                  <td>790 JD</td>
                  <td>Aug 22, 2026</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

