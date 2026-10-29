
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../../Controllers/admin-login.php';

$currentPage = 'admin-login';
$pageTitle = 'Admin access — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../Includes/header.php';
?>

<main class="auth-shell admin-auth">

  <a class="logo" href="../Layout/routing.php?page=home">
    <span class="logo-mark"></span>NEXORA
    <span class="admin-word">CONTROL</span>
  </a>

  <section class="auth-card">

    <span class="eyebrow">RESTRICTED ACCESS // ADMIN</span>

    <h1>Command <span class="text-grad">center.</span></h1>

    <p class="muted">Authorized personnel only.</p>

    <?php foreach ($errors as $error): ?>
      <p style="color: #ef4444;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endforeach; ?>

    <form
      method="POST"
      action="/Nexora/View/Layout/routing.php?page=admin-login"
    >

      <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(
          $_SESSION['admin_csrf_token'],
          ENT_QUOTES,
          'UTF-8'
        ) ?>"
      />

      <label class="field">
        <span>Admin email</span>
        <input
          class="input"
          type="email"
          name="email"
          placeholder="admin@nexora.tech"
          value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
          required
        />
      </label>

      <label class="field">
        <span>Password</span>
        <input
          class="input"
          type="password"
          name="password"
          placeholder="••••••••"
          required
        />
      </label>

      <button class="btn btn-primary full" type="submit">
        Access dashboard
      </button>

    </form>

    <a
      class="back-link"
      href="../Layout/routing.php?page=home"
    >
      ← Return to storefront
    </a>

  </section>

</main>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
