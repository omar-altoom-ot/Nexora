<?php
$currentPage = 'admin-orders';
$pageTitle = 'Orders - NEXORA Admin';
$pageStylesheet = 'pages.css'; 
$isAdminPage = true;
$showStoreFooter = false;

// 1. Include the Database class
require_once __DIR__ . '/../../Config/DBconnection.php';

// 2. Include the Order class
require_once __DIR__ . '/../../Controllers/orders.php';

// 3. Build connection
$orderController = new OrderController(new Db());

// Handle Admin Cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_cancel_id'])) {
    $cancelId = (int)$_POST['admin_cancel_id'];
    $orderController->updateOrderStatus($cancelId, 'cancelled');
    header("Location: ../Layout/routing.php?page=admin-orders");
    exit;
}

// ---- Read query params ----
$status = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$p      = max(1, (int)($_GET['p'] ?? 1));
$limit  = 10;
$offset = ($p - 1) * $limit;

// ---- Fetch data ----
$orders      = $orderController->getOrdersFiltered($status, $search, $limit, $offset);
$totalOrders = $orderController->countOrdersFiltered($status, $search);
$totalPages  = max(1, (int)ceil($totalOrders / $limit));

// Map DB statuses to badge classes
function badgeClass(string $status): string {
    return match($status) {
        'delivered' => 'status-green',
        'confirmed' => 'status-blue',
        'processing'=> 'status-blue',
        'shipped'   => 'status-pink',
        'pending'   => 'status-amber',
        'cancelled' => 'status-red',
        default     => 'status-blue',
    };
}

// 4. Include the HTML header
require_once __DIR__ . '/../Includes/header.php';
?>

<!-- 5. Add the Orders-specific CSS file right after the header -->
<link rel="stylesheet" href="../css/orders.css" />

<?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
<main class="admin-main">
  <header class="admin-top">
    <div>
      <small>ADMIN CONSOLE / <span>ORDERS</span></small>
      <h2>Orders</h2>
    </div>
    <div class="admin-user">
      <span class="live-dot"></span><span>System live</span>
      <a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
    </div>
  </header>
  <div class="admin-content">
    <div class="admin-welcome">
      <div>
        <span class="eyebrow">// FULFILLMENT CENTER</span>
        <h1>Order <span class="text-grad">stream.</span></h1>
        <p class="muted">Track and manage every customer order.</p>
      </div>
      <button class="btn btn-ghost btn-sm" onclick="exportTableToCSV('orders-export.csv')">↓ Export orders</button>
    </div>

    <section class="card panel">
      <!-- Search and Filter Toolbar -->
      <form method="get" action="../Layout/routing.php" class="table-toolbar">
        <input type="hidden" name="page" value="admin-orders">

        <input class="input" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Search order or customer...">

        <select class="input" name="status" onchange="this.form.submit()" style="width: 180px;">
          <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All statuses</option>
          <?php foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
              <?= ucfirst($s) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button class="btn btn-ghost btn-sm" type="submit">Search</button>
      </form>

      <!-- Data Table -->
      <div class="table-wrap">
        <table id="ordersTable">
          <thead>
            <tr>
              <th>ORDER ID</th><th>CUSTOMER</th><th>DATE</th>
              <th>ITEMS</th><th>TOTAL</th><th>STATUS</th><th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$orders): ?>
              <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">No orders found.</td></tr>
            <?php else: foreach ($orders as $o):
                $customer = htmlspecialchars($o->first_name . ' ' . $o->last_name);
                $date     = date('M d, Y', strtotime($o->order_date));
                $total    = number_format((float)$o->total_amount, 2) . ' JD';
            ?>
              <tr>
                <td>#NX-<?= (int)$o->id ?></td>
                <td><?= $customer ?></td>
                <td><?= $date ?></td>
                <td><?= (int)$o->items_count ?></td>
                <td><?= $total ?></td>
                <td><span class="status <?= badgeClass($o->status) ?>"><?= ucfirst($o->status) ?></span></td>
                <td>
                  <div class="action-buttons">
                    <a href="../Layout/routing.php?page=admin-order-details&id=<?= (int)$o->id ?>">View →</a>
                    
                    <?php if ($o->status !== 'cancelled' && $o->status !== 'delivered'): ?>
                      <!-- We pass the Order ID to the JS function -->
                      <button type="button" class="btn-cancel-text" onclick="openModal(<?= (int)$o->id ?>)">Cancel</button>
                      
                      <!-- The hidden form that will be submitted by the modal -->
                      <form id="cancel-form-<?= (int)$o->id ?>" method="POST" style="display:none;">
                          <input type="hidden" name="admin_cancel_id" value="<?= (int)$o->id ?>">
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <?php
      // Build the base URL properly so pagination works with the routing system
      $baseUrl = '../Layout/routing.php?page=admin-orders'
               . '&status=' . urlencode($status)
               . '&q='      . urlencode($search);

      $prevPage = max(1, $p - 1);
      $nextPage = min($totalPages, $p + 1);
      ?>

      <!-- Pagination -->
      <div class="orders-pagination">
        <a class="page-arrow <?= $p <= 1 ? 'disabled' : '' ?>"
           <?php if ($p > 1): ?> href="<?= $baseUrl ?>&p=<?= $prevPage ?>" <?php endif; ?>
           aria-label="Previous page">←</a>

        <span class="page-info">Page <?= $p ?> / <?= $totalPages ?></span>

        <a class="page-arrow <?= $p >= $totalPages ? 'disabled' : '' ?>"
           <?php if ($p < $totalPages): ?> href="<?= $baseUrl ?>&p=<?= $nextPage ?>" <?php endif; ?>
           aria-label="Next page">→</a>
      </div>
    </section>
  </div>
</main>

<!-- Custom Cancel Confirmation Modal -->
<div id="cancel-modal" class="modal-overlay">
  <div class="modal-content">
    <h3>Cancel Order?</h3>
    <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
    <div class="modal-actions">
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal()">Keep Order</button>
      <button type="button" class="btn btn-primary btn-sm" id="confirm-cancel-btn" style="background: #fb7185; box-shadow: 0 0 15px rgba(251,113,133,0.4);">Yes, Cancel</button>
    </div>
  </div>
</div>

<!-- Export to CSV Script & Modal Logic -->
<script>
// --- Modal Logic ---
const modal = document.getElementById('cancel-modal');
let currentFormId = null;

function openModal(orderId) {
    currentFormId = 'cancel-form-' + orderId;
    modal.style.display = 'flex'; 
}

function closeModal() {
    modal.style.display = 'none'; 
    currentFormId = null;
}

// When the user clicks "Yes, Cancel" in the modal
document.getElementById('confirm-cancel-btn').addEventListener('click', function() {
    if (currentFormId) {
        document.getElementById(currentFormId).submit(); 
    }
});

// Close modal if user clicks outside the box
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        closeModal();
    }
});

// --- Export Logic ---
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#ordersTable tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length - 1; j++) { // -1 ignores the "Action" column
            var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s)/gm, " ");
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(","));
    }

    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>