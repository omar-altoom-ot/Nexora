
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

    <?php if (!empty($success)): ?>
      <p style="color: #22c55e;">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endif; ?>

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

        <select class="input" id="customer-status-filter">
          <option value="all">All customers</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
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
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>

          <tbody>

            <?php if (empty($customers)): ?>

              <tr>
                <td colspan="8" style="text-align: center;">
                  No customers found.
                </td>
              </tr>

            <?php else: ?>

              <?php foreach ($customers as $customer): ?>

                <?php
                  $isActive = (int) $customer['is_active'] === 1;
                ?>

                <tr
                  class="customer-row"
                  data-status="<?= $isActive ? 'active' : 'inactive' ?>">

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

                  <td>
                    <?= (int) $customer['orders_count'] ?>
                  </td>

                  <td>
                    $<?= number_format(
                      (float) $customer['lifetime_value'],
                      2
                    ) ?>
                  </td>

                  <td>—</td>

                  <td>
                    <?php if ($isActive): ?>
                      <span style="color: #22c55e;">
                        Active
                      </span>
                    <?php else: ?>
                      <span style="color: #ef4444;">
                        Inactive
                      </span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <form
                      method="POST"
                      action="/Nexora/View/Layout/routing.php?page=admin-customers"
                      onsubmit="return confirm('Are you sure you want to change this customer account status?');">

                      <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                          $_SESSION['admin_customers_csrf_token'] ?? '',
                          ENT_QUOTES,
                          'UTF-8'
                        ) ?>">

                      <input
                        type="hidden"
                        name="customer_id"
                        value="<?= (int) $customer['id'] ?>">

                      <input
                        type="hidden"
                        name="action"
                        value="<?= $isActive ? 'deactivate' : 'reactivate' ?>">

                      <button
                        class="btn btn-ghost btn-sm"
                        type="submit">

                        <?= $isActive ? 'Deactivate' : 'Reactivate' ?>

                      </button>

                    </form>
                  </td>

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
    const statusFilter = document.getElementById('customer-status-filter');
    const customerRows = document.querySelectorAll('.customer-row');

    function filterCustomers() {

        const searchValue = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        customerRows.forEach(function (row) {

            const customerText = row.textContent.toLowerCase();

            const matchesSearch = customerText.includes(searchValue);

            const matchesStatus =
                selectedStatus === 'all' ||
                row.dataset.status === selectedStatus;

            row.style.display =
                matchesSearch && matchesStatus ? '' : 'none';

        });
    }

    searchInput.addEventListener('input', filterCustomers);
    statusFilter.addEventListener('change', filterCustomers);

});
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
