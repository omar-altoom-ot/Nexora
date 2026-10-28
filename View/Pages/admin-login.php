<?php
$currentPage = 'admin-login';
$pageTitle = 'Admin access — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <main class="auth-shell admin-auth">
      <a class="logo" href="home.php"
        ><span class="logo-mark"></span>NEXORA
        <span class="admin-word">CONTROL</span></a
      >
      <section class="auth-card">
        <span class="eyebrow">RESTRICTED ACCESS // ADMIN</span>
        <h1>Command <span class="text-grad">center.</span></h1>
        <p class="muted">Authorized personnel only.</p>
        <form>
          <label class="field"
            ><span>Admin email</span
            ><input
              class="input"
              type="email"
              placeholder="admin@nexora.tech"
              required /></label
          ><label class="field"
            ><span>Password</span
            ><input
              class="input"
              type="password"
              placeholder="••••••••"
              required /></label
          ><a class="btn btn-primary full" href="admin-overview.php"
            >Access dashboard</a
          >
        </form>
        <a class="back-link" href="home.php">← Return to storefront</a>
      </section>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
