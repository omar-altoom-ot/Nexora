<?php
// Abdullah ALqudah

require_once __DIR__ . '/../../Controllers/account.php';

$currentPage = 'account';
$pageTitle = 'My account — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

$fullName = $customer
  ? trim($customer['first_name'] . ' ' . $customer['last_name'])
  : 'Customer';

$initials = $customer
  ? strtoupper(
    substr($customer['first_name'], 0, 1) .
      substr($customer['last_name'], 0, 1)
  )
  : '--';

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
          >      <a href="../Layout/routing.php?page=my_orders">My orders</a>
<a href="../Layout/routing.php?page=wishlist">Wishlist</a
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


      <article class="card profile-card" id="settings">

        <div class="row-between">
          <div>
            <h2>Personal information</h2>
            <p class="muted">Manage your account details.</p>
          </div>

          <button
            class="btn btn-ghost btn-sm"
            type="button"
            id="edit-profile-btn">
            Edit profile
          </button>
        </div>

        <?php if ($success !== ''): ?>
          <p style="color: #22c55e;">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
          </p>
        <?php endif; ?>

        <div class="profile-grid" id="profile-view">

          <div>
            <small>FULL NAME</small>
            <b><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></b>
          </div>

          <div>
            <small>EMAIL</small>
            <b><?= htmlspecialchars($customer['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></b>
          </div>

          <div>
            <small>PHONE</small>
            <b><?= htmlspecialchars($customer['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></b>
          </div>

          <div>
            <small>DEFAULT ADDRESS</small>
            <b>—</b>
          </div>

        </div>


        <form
          id="profile-edit-form"
          method="POST"
          action="/Nexora/View/Layout/routing.php?page=account#settings"
          style="display: <?= !empty($errors) ? 'block' : 'none' ?>;">

          <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                      $_SESSION['account_csrf_token'],
                      ENT_QUOTES,
                      'UTF-8'
                    ) ?>">

          <div class="profile-grid">

            <label class="field">
              <span>First name</span>
              <input
                class="input"
                type="text"
                name="first_name"
                maxlength="100"
                value="<?= htmlspecialchars(
                          $formData['first_name'] ?? $customer['first_name'] ?? '',
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>"
                required>
            </label>

            <label class="field">
              <span>Last name</span>
              <input
                class="input"
                type="text"
                name="last_name"
                maxlength="100"
                value="<?= htmlspecialchars(
                          $formData['last_name'] ?? $customer['last_name'] ?? '',
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>"
                required>
            </label>

            <label class="field">
              <span>Email address</span>
              <input
                class="input"
                type="email"
                name="email"
                maxlength="255"
                value="<?= htmlspecialchars(
                          $formData['email'] ?? $customer['email'] ?? '',
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>"
                required>
            </label>

            <label class="field">
              <span>Phone number</span>
              <input
                class="input"
                type="tel"
                name="phone"
                maxlength="20"
                value="<?= htmlspecialchars(
                          $formData['phone'] ?? $customer['phone'] ?? '',
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>">
            </label>

          </div>

          <div style="display: flex; gap: 12px; margin-top: 20px;">

            <button class="btn btn-primary" type="submit">
              Save changes
            </button>

            <button
              class="btn btn-ghost"
              type="button"
              id="cancel-edit-btn">
              Cancel
            </button>

          </div>

        </form>


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
                <td colspan="5" style="text-align: center;">
                  Order history will appear here.
                </td>
              </tr>
            </tbody>

          </table>

        </div>

      </article>

    </section>

  </div>

</main>

<nav class="page-links" aria-label="Store pages">
  <a href="../Layout/routing.php?page=home">Home</a>
  <a href="../Layout/routing.php?page=products">Products</a>
  <a href="../Layout/routing.php?page=cart">Cart</a>
  <a href="../Layout/routing.php?page=checkout">Checkout</a>
  <a href="../Layout/routing.php?page=account">My account</a>
  <a href="../Layout/routing.php?page=wishlist">Wishlist</a>
  <a href="../Layout/routing.php?page=login">Login</a>
  <a href="../Layout/routing.php?page=register">Register</a>
</nav>


<script>
  document.addEventListener('DOMContentLoaded', function() {

    const editBtn = document.getElementById('edit-profile-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    const profileView = document.getElementById('profile-view');
    const editForm = document.getElementById('profile-edit-form');

    function showEditForm() {
      profileView.style.display = 'none';
      editForm.style.display = 'block';
      editBtn.style.display = 'none';
    }

    function hideEditForm() {
      profileView.style.display = '';
      editForm.style.display = 'none';
      editBtn.style.display = '';
      editForm.reset();
    }

    editBtn.addEventListener('click', showEditForm);

    cancelBtn.addEventListener('click', hideEditForm);

    if (editForm.style.display !== 'none') {
      showEditForm();
    }

  });
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>