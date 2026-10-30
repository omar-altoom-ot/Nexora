<?php
$currentPage = 'admin-coupons';
$pageTitle = 'Coupons — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';

$couponController = new CouponController(new Db());
$errorMessage = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $result = $couponController->create(
            $_POST['code'],
            $_POST['discount_type'],
            (float)$_POST['discount_value'],
            (float)$_POST['min_order_amount'],
            $_POST['valid_until'],
            (int)$_POST['usage_limit']
        );
        
        if ($result === false) {
            $errorMessage = "Invalid date: The expiry date must be today or in the future.";
        } else {
            header("Location: ../Layout/routing.php?page=admin-coupons");
            exit;
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $couponController->delete((int)$_POST['id']);
        header("Location: ../Layout/routing.php?page=admin-coupons");
        exit;
    }
}

$coupons = $couponController->getAll();
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
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('create-modal').style.display='flex'">＋ Create coupon</button>
    </div>

    <?php if ($errorMessage): ?>
        <div style="background: rgba(251, 113, 133, 0.1); border: 1px solid #fb7185; color: #fb7185; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
            <?= htmlspecialchars($errorMessage) ?>
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
              <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">No coupons found.</td></tr>
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
                  <span class="status <?= $c->is_active ? 'status-green' : 'status-red' ?>">
                    <?= $c->is_active ? 'Active' : 'Expired' ?>
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
    </section>
  </div>
</main>

<!-- CREATE MODAL -->
<div id="create-modal" class="modal-overlay">
  <div class="modal-content">
    <h3>Create Coupon</h3>
    <form method="POST">
      <input type="hidden" name="action" value="create">
      
      <label class="field">
        <span>Coupon Code</span>
        <input class="input" name="code" placeholder="e.g. SUMMER20" required>
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
        <button type="submit" class="btn btn-primary btn-sm">Save Coupon</button>
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
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>