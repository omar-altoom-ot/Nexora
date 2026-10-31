
<?php
// Abdullah ALqudah

require_once __DIR__ . '/../../Controllers/admin-customers.php';

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
      <span class="live-dot"></span>
      <span>System live</span>
      <a class="admin-avatar"
         href="../Layout/routing.php?page=admin-profile">AD</a>
    </div>
  </header>

  <div class="admin-content">

    <div class="admin-welcome">
      <div>
        <span class="eyebrow">// COMMUNITY INTEL</span>
        <h1>Customer <span class="text-grad">base.</span></h1>
        <p class="muted">
          Understand and support your NEXORA community.
        </p>
      </div>

      <button class="btn btn-ghost btn-sm" type="button" disabled>
        ↓ Export list
      </button>
    </div>

    <?php foreach ($errors as $error): ?>
      <p style="color: #ef4444;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endforeach; ?>

    <div class="grid grid-3 kpi-grid">

      <div class="kpi card">
        <small>TOTAL CUSTOMERS</small>
        <b><?= count($customers) ?></b>
      </div>

      <div class="kpi card">
        <small>NEW THIS MONTH</small>
        <b>—</b>
      </div>

      <div class="kpi card">
        <small>RETURNING CUSTOMERS</small>
        <b>—</b>
      </div>

    </div>

    <section class="card panel">

      <div class="table-toolbar">

        <input
          class="input"
          type="search"
          id="customer-search"
          placeholder="Search name or email..."
        />

        <select class="input">
          <option>All customers</option>
        </select>

      </div>

      <div class="table-wrap">

        <table id="customers-table">

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

            <?php if (empty($customers)): ?>

              <tr>
                <td colspan="6" style="text-align: center;">
                  No customers found.
                </td>
              </tr>

            <?php else: ?>

              <?php foreach ($customers as $customer): ?>

                <tr class="customer-row">

                  <td>
                    <?= htmlspecialchars(
                      $customer['first_name'] . ' ' . $customer['last_name'],
                      ENT_QUOTES,
                      'UTF-8'
                    ) ?>
                  </td>

                  <td>
                    <?= htmlspecialchars(
                      $customer['email'],
                      ENT_QUOTES,
                      'UTF-8'
                    ) ?>
                  </td>

                  <td>
                    <?= htmlspecialchars(
                      $customer['phone'] ?? '—',
                      ENT_QUOTES,
                      'UTF-8'
                    ) ?>
                  </td>

                  <td>—</td>

                  <td>—</td>

                  <td>—</td>

                </tr>

              <?php endforeach; ?>

            <?php endif; ?>

          </tbody>

        </table>

      </div>

    </section>

  </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('customer-search');
    const customerRows = document.querySelectorAll('.customer-row');

    searchInput.addEventListener('input', function () {

        const searchValue = this.value.toLowerCase().trim();

        customerRows.forEach(function (row) {

            const customerText = row.textContent.toLowerCase();

            row.style.display = customerText.includes(searchValue)
                ? ''
                : 'none';

        });

    });

});
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
