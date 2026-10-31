<?php
// API for real-time Javascript check
if (isset($_GET['check_code'])) {
    require_once __DIR__ . '/../../Config/DBconnection.php';
    require_once __DIR__ . '/../../Controllers/CouponController.php';
    $cc = new CouponController(new Db());
    echo $cc->checkCodeExists(trim($_GET['check_code'])) ? 'exists' : 'available';
    exit;
}

$currentPage = 'admin-coupons';
$pageTitle = 'Coupons — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';

$couponController = new CouponController(new Db());
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $result = $couponController->create(
            trim($_POST['code']),
            $_POST['discount_type'],
            (float)$_POST['discount_value'],
            (float)$_POST['min_order_amount'],
            $_POST['valid_until'],
            (int)$_POST['usage_limit']
        );

        if ($result === false) {
            $errorMessage = "Invalid date: The expiry date must be today or in the future.";
        } elseif ($result === 'duplicate') {
            $errorMessage = "The coupon code '<b>" . htmlspecialchars($_POST['code']) . "</b>' already exists.";
        } else {
            header("Location: ../Layout/routing.php?page=admin-coupons");
            exit;
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $couponController->delete((int)$_POST['id']);
        header("Location: ../Layout/routing.php?page=admin-coupons&filter=" . ($_GET['filter'] ?? 'all') . "&p=" . ($_GET['p'] ?? 1));
        exit;
    }
}

$filter = $_GET['filter'] ?? 'all';
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$totalCoupons = $couponController->countAdminCoupons($filter);
$totalPages = ceil($totalCoupons / $limit);
$coupons = $couponController->getAdminCoupons($filter, $limit, $offset);

require_once __DIR__ . '/../Includes/header.php';
?>

<link rel="stylesheet" href="../css/orders.css" />

<?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
<main class="admin-main">
  <header class="admin-top">
    <div>
      <small>ADMIN CONSOLE / <span>COUPONS</span></small>
      <h2>Coupons</h2>
    </div>
    <div class="admin-user">
      <span class="live-dot"></span><span>System live</span>
      <a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
    </div>
  </header>

  <div class="admin-content">
    <div class="admin-welcome">
      <div>
        <span class="eyebrow">// GROWTH ENGINE</span>
        <h1>Coupons <span class="text-grad">&amp; sales.</span></h1>
        <p class="muted">Create focused offers and track their performance.</p>
      </div>
      <div style="display: flex; gap: 12px; align-items: center;">
        
        <!-- FIXED DROPDOWN -->
        <form method="GET" action="../Layout/routing.php" style="margin: 0; display: flex;">
            <input type="hidden" name="page" value="admin-coupons">
            <select name="filter" class="input" style="padding: 0 35px 0 15px !important; width: 165px; height: 38px; font-size: 0.85rem; cursor: pointer; background-color: var(--panel); color: var(--text); border: 1px solid var(--line); background-image: none; -webkit-appearance: none; -moz-appearance: none; appearance: none;" onchange="this.form.submit()">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Coupons</option>
                <option value="valid" <?= $filter === 'valid' ? 'selected' : '' ?>>Valid Only</option>
                <option value="expired" <?= $filter === 'expired' ? 'selected' : '' ?>>Expired Only</option>
            </select>
            <!-- Add the custom cyan arrow back manually so it's not cramped -->
            <style>
                select[name="filter"] {
                    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2322d3ee' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") !important;
                    background-repeat: no-repeat !important;
                    background-position: right 12px center !important;
                    background-size: 16px !important;
                }
            </style>
        </form>
        
        <button class="btn btn-primary btn-sm" onclick="openCreateModal()">＋ Create coupon</button>
      </div>
    </div>

    <?php if ($errorMessage): ?>
        <div style="background: rgba(251, 113, 133, 0.1); border: 1px solid #fb7185; color: #fb7185; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
            <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <section class="card panel">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>CODE</th>
              <th>OFFER</th>
              <th>MIN ORDER</th>
              <th>VALID UNTIL</th>
              <th>USAGE</th>
              <th>STATUS</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$coupons): ?>
              <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">No coupons found matching this filter.</td></tr>
            <?php else: foreach ($coupons as $c): ?>
              <tr>
                <td><b><?= htmlspecialchars($c->code) ?></b></td>
                <td>
                  <?= $c->discount_type === 'percentage' ? $c->discount_value . '% off' : $c->discount_value . ' JD off' ?>
                </td>
                <td><?= number_format($c->min_order_amount, 2) ?> JD</td>
                <td><?= date('M d, Y', strtotime($c->valid_until)) ?></td>
                <td><?= $c->used_count ?> / <?= $c->usage_limit ?></td>
                <td>
                  <?php
                    $isExpired = strtotime($c->valid_until) < strtotime(date('Y-m-d')) || $c->used_count >= $c->usage_limit || !$c->is_active;
                  ?>
                  <span class="status <?= !$isExpired ? 'status-green' : 'status-red' ?>">
                    <?= !$isExpired ? 'Active' : 'Expired' ?>
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button type="button" class="btn-cancel-text" onclick="openModal(<?= $c->id ?>)">Delete</button>
                    <form id="delete-form-<?= $c->id ?>" method="POST" style="display:none;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $c->id ?>">
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
      <div style="padding: 15px; border-top: 1px solid var(--line); display: flex; justify-content: center; gap: 10px;">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="../Layout/routing.php?page=admin-coupons&filter=<?= $filter ?>&p=<?= $i ?>"
                 class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-ghost' ?>"
                 style="min-width: 35px; padding: 5px 10px;">
                  <?= $i ?>
              </a>
          <?php endfor; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<!-- CREATE MODAL -->
<div id="create-modal" class="modal-overlay">
  <div class="modal-content">
    <h3>Create Coupon</h3>
    <form method="POST" id="create-coupon-form">
      <input type="hidden" name="action" value="create">

      <label class="field">
        <span>Coupon Code</span>
        <input class="input" name="code" id="coupon-code-input" placeholder="e.g. SUMMER20" required autocomplete="off">
        <small id="code-error" style="color: #fb7185; display: none; margin-top: 5px; font-weight: bold;">This code already exists. Try a different one.</small>
      </label>

      <div class="form-grid">
        <label class="field">
          <span>Type</span>
          <select class="input" name="discount_type">
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed Amount (JD)</option>
          </select>
        </label>
        <label class="field">
          <span>Value</span>
          <input class="input" type="number" step="0.01" name="discount_value" required>
        </label>
      </div>

      <div class="form-grid">
        <label class="field">
          <span>Min Order Amount (JD)</span>
          <input class="input" type="number" step="0.01" name="min_order_amount" value="0" required>
        </label>
        <label class="field">
          <span>Valid Until</span>
          <input class="input" type="date" name="valid_until" min="<?= date('Y-m-d') ?>" required>
        </label>
      </div>

      <label class="field">
        <span>Usage Limit</span>
        <input class="input" type="number" name="usage_limit" value="100" required>
      </label>

      <div class="modal-actions" style="margin-top: 20px;">
        <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('create-modal').style.display='none'">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm" id="save-btn">Save Coupon</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div id="cancel-modal" class="modal-overlay">
  <div class="modal-content">
    <h3>Delete Coupon?</h3>
    <p>Are you sure you want to permanently delete this coupon?</p>
    <div class="modal-actions">
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal()">Cancel</button>
      <button type="button" class="btn btn-primary btn-sm" id="confirm-delete-btn" style="background: #fb7185;">Yes, Delete</button>
    </div>
  </div>
</div>

<script>
let deleteFormId = null;

function openModal(id) {
    deleteFormId = 'delete-form-' + id;
    document.getElementById('cancel-modal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('cancel-modal').style.display = 'none';
    deleteFormId = null;
}
document.getElementById('confirm-delete-btn').addEventListener('click', function() {
    if (deleteFormId) {
        document.getElementById(deleteFormId).submit();
    }
});

function openCreateModal() {
    document.getElementById('create-coupon-form').reset();
    document.getElementById('code-error').style.display = 'none';
    document.getElementById('coupon-code-input').style.borderColor = 'var(--line)';
    document.getElementById('save-btn').disabled = false;
    document.getElementById('create-modal').style.display = 'flex';
}

let checkTimeout;
document.getElementById('coupon-code-input').addEventListener('input', function() {
    const code = this.value.trim();
    const errorMsg = document.getElementById('code-error');
    const saveBtn = document.getElementById('save-btn');

    clearTimeout(checkTimeout);

    if (code === '') {
        errorMsg.style.display = 'none';
        this.style.borderColor = 'var(--line)';
        saveBtn.disabled = false;
        return;
    }

    checkTimeout = setTimeout(() => {
        const routingUrl = window.location.pathname + '?page=admin-coupons&check_code=' + encodeURIComponent(code);
        fetch(routingUrl)
            .then(response => response.text())
            .then(result => {
                const res = result.trim();
                if (res === 'exists') {
                    errorMsg.style.display = 'block';
                    this.style.borderColor = '#fb7185';
                    saveBtn.disabled = true;
                } else {
                    errorMsg.style.display = 'none';
                    this.style.borderColor = 'var(--green)';
                    saveBtn.disabled = false;
                }
            })
            .catch(err => console.error('Validation error:', err));
    }, 300);
});
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>