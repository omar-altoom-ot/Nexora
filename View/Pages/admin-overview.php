<?php
$currentPage = 'admin-overview';
$pageTitle = 'Overview — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>OVERVIEW</span></small>
          <h2>Overview</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// COMMAND OVERVIEW</span>
            <h1>Good morning, <span class="text-grad">Alex.</span></h1>
            <p class="muted">Here’s what’s happening across NEXORA today.</p>
          </div>
          <button class="btn btn-ghost btn-sm">↓ Export report</button>
        </div>
        <div class="grid grid-4 kpi-grid">
          <div class="kpi card">
            <small>GROSS REVENUE <span>↗ 12.8%</span></small
            ><b>JD 48,290</b>
            <div class="sparkline">▁▂▂▃▂▄▃▅▄▆▅▇</div>
          </div>
          <div class="kpi card">
            <small>ORDERS <span>↗ 8.2%</span></small
            ><b>1,284</b>
            <div class="sparkline cyan">▂▁▃▂▄▃▅▄▆▇▆█</div>
          </div>
          <div class="kpi card">
            <small>ACTIVE CUSTOMERS <span>↗ 5.4%</span></small
            ><b>8,642</b>
            <div class="sparkline blue">▁▂▃▂▃▄▃▅▆▅▇█</div>
          </div>
          <div class="kpi card">
            <small>LOW STOCK ALERTS <span class="warn">!</span></small
            ><b>07</b>
            <div class="kpi-note">Requires attention</div>
          </div>
        </div>
        <div class="admin-panels">
          <section class="card panel chart-panel">
            <div class="row-between">
              <div>
                <h3>Revenue performance</h3>
                <p class="muted">Net sales · last 30 days</p>
              </div>
              <select class="input">
                <option>Last 30 days</option>
                <option>Last 7 days</option>
              </select>
            </div>
            <div class="chart">
              <div class="chart-grid">
                <span>10k</span><span>7.5k</span><span>5k</span><span>2.5k</span
                ><span>0</span>
              </div>
              <svg
                viewBox="0 0 700 220"
                preserveAspectRatio="none"
                aria-label="Revenue trending upward"
              >
                <defs>
                  <linearGradient id="area" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0" stop-color="#8b5cf6" stop-opacity=".38" />
                    <stop offset="1" stop-color="#8b5cf6" stop-opacity="0" />
                  </linearGradient>
                </defs>
                <path
                  d="M0 180 C45 160 55 175 100 145 S165 160 205 120 S270 145 315 110 S370 132 420 80 S475 105 520 70 S585 95 630 42 S675 52 700 20 V220 H0Z"
                  fill="url(#area)"
                />
                <path
                  d="M0 180 C45 160 55 175 100 145 S165 160 205 120 S270 145 315 110 S370 132 420 80 S475 105 520 70 S585 95 630 42 S675 52 700 20"
                  fill="none"
                  stroke="#a78bfa"
                  stroke-width="3"
                  vector-effect="non-scaling-stroke"
                />
              </svg>
            </div>
            <div class="chart-months">
              <span>OCT 01</span><span>OCT 07</span><span>OCT 14</span
              ><span>OCT 21</span><span>OCT 28</span>
            </div>
          </section>
          <section class="card panel">
            <div class="row-between">
              <div>
                <h3>Order status</h3>
                <p class="muted">1,284 total orders</p>
              </div>
              <a href="../Layout/routing.php?page=admin-orders">View all →</a>
            </div>
            <div class="donut-wrap">
              <div class="donut">
                <span>1,284<small>ORDERS</small></span>
              </div>
              <div class="legend">
                <span><i class="green-dot"></i>Delivered <b>68%</b></span
                ><span><i class="blue-dot"></i>Processing <b>21%</b></span
                ><span><i class="pink-dot"></i>Cancelled <b>11%</b></span>
              </div>
            </div>
          </section>
        </div>
        <section class="card panel">
          <div class="row-between">
            <div>
              <h3>Latest orders</h3>
              <p class="muted">Recent customer activity</p>
            </div>
            <a href="../Layout/routing.php?page=admin-orders">All orders →</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ORDER ID</th>
                  <th>CUSTOMER</th>
                  <th>DATE</th>
                  <th>AMOUNT</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#NX-2084</td>
                  <td>Samir Haddad</td>
                  <td>Oct 28, 2026</td>
                  <td>1,288 JD</td>
                  <td><span class="status status-blue">Processing</span></td>
                </tr>
                <tr>
                  <td>#NX-2083</td>
                  <td>Lina Saleh</td>
                  <td>Oct 28, 2026</td>
                  <td>349 JD</td>
                  <td><span class="status status-green">Delivered</span></td>
                </tr>
                <tr>
                  <td>#NX-2082</td>
                  <td>Omar Nasser</td>
                  <td>Oct 27, 2026</td>
                  <td>89 JD</td>
                  <td><span class="status status-amber">Pending</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

