<?php
$currentPage = 'admin-profile';
$pageTitle = 'Admin profile — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>ACCOUNT SETTINGS</span></small>
          <h2>Admin profile</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="admin-profile.php">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// ACCOUNT SETTINGS</span>
            <h1>Admin <span class="text-grad">profile.</span></h1>
            <p class="muted">Manage your administrator identity and access.</p>
          </div>
        </div>
        <div class="profile-admin-layout">
          <section class="card panel">
            <div class="profile-hero">
              <div class="avatar admin-big">AD</div>
              <div>
                <h2>Alex Doe</h2>
                <p class="muted">Super administrator · Joined Jan 2026</p>
              </div>
            </div>
            <h3>Personal details</h3>
            <div class="form-grid">
              <label class="field"
                ><span>Full name</span
                ><input class="input" value="Alex Doe" /></label
              ><label class="field"
                ><span>Email</span
                ><input class="input" type="email" value="admin@nexora.tech"
              /></label>
            </div>
            <button class="btn btn-primary btn-sm">Save changes</button>
          </section>
          <section class="card panel">
            <h3>Security</h3>
            <p class="muted">Keep your admin account protected.</p>
            <label class="field"
              ><span>Current password</span
              ><input
                class="input"
                type="password"
                placeholder="••••••••" /></label
            ><label class="field"
              ><span>New password</span
              ><input
                class="input"
                type="password"
                placeholder="At least 12 characters" /></label
            ><button class="btn btn-ghost btn-sm">Update password</button>
          </section>
        </div>
        <section class="card panel access-card">
          <span class="live-dot"></span>
          <div>
            <h3>Administrator access</h3>
            <p class="muted">
              Full access to products, orders, customers, and analytics.
            </p>
          </div>
          <span class="status status-green">Protected</span>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
