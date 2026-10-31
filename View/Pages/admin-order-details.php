<?php
$currentPage = 'admin-orders'; // Keep the sidebar highlighting "Orders"
$pageTitle = 'Order Details — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;

// ADDED: Auto-refresh every 5 seconds to see real-time status updates
header("Refresh: 5");

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/orders.php';

$orderController = new OrderController(new Db());
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $orderController->getOrderById($orderId);

// If no order found, redirect back to the orders list
if (!$order) {
    header("Location: ../Layout/routing.php?page=admin-orders");
    exit;
}

// Fetch the items in this order
$items = $orderController->getOrderItems($orderId);

// Helper for badges
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

require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
<main class="admin-main">
  <header class="admin-top">
    <div>
      <small>ADMIN CONSOLE / <span>ORDERS / DETAILS</span></small>
      <h2>Order #NX-<?= (int)$order->id ?></h2>
    </div>
    <div class="admin-user">
      <span class="live-dot"></span><span>System live (Auto-refreshing)</span>
      <a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
    </div>
  </header>

  <div class="admin-content">
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
      <a href="../Layout/routing.php?page=admin-orders" class="btn btn-ghost btn-sm">← Back to Orders</a>
    </div>

    <div class="admin-panels">

      <!-- Customer & Shipping Info -->
      <section class="card panel">
        <h3>Customer Information</h3>
        <div class="profile-grid" style="margin-top: 20px;">
          <div>
            <small>FULL NAME</small>
            <b><?= htmlspecialchars($order->first_name . ' ' . $order->last_name) ?></b>
          </div>
          <div>
            <small>EMAIL</small>
            <b><?= htmlspecialchars($order->email) ?></b>
          </div>
          <div>
            <small>PHONE</small>
            <b><?= htmlspecialchars($order->phone ?? 'N/A') ?></b>
          </div>
          <div>
            <small>ORDER DATE</small>
            <b><?= date('M d, Y - h:i A', strtotime($order->order_date)) ?></b>
          </div>
        </div>

        <h3 style="margin-top: 30px;">Shipping Address</h3>
        <p class="muted" style="margin-top: 10px; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid var(--line);">
          <?= nl2br(htmlspecialchars($order->shipping_address)) ?>
        </p>
      </section>

      <!-- Order Status -->
      <section class="card panel">
        <h3>Order Status</h3>
        <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 15px;">
          <div>
            <small style="color:var(--muted); font-size: 0.8rem;">CURRENT STATUS</small><br>
            <span class="status <?= badgeClass($order->status) ?>" style="font-size: 1rem; margin-top: 5px;">
              <?= ucfirst($order->status) ?>
            </span>
          </div>
          <div>
            <small style="color:var(--muted); font-size: 0.8rem;">TOTAL AMOUNT</small><br>
            <b style="font-size: 1.5rem; color: var(--cyan);">
              <?= number_format((float)$order->total_amount, 2) ?> JD
            </b>
          </div>
        </div>
      </section>

    </div>

    <!-- Order Items Table -->
    <section class="card panel" style="margin-top: 20px;">
      <div class="row-between">
        <div>
          <h3>Items Purchased</h3>
          <p class="muted">Products included in this order</p>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>PRODUCT NAME</th>
              <th>UNIT PRICE</th>
              <th>QUANTITY</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $subtotal = 0;
            foreach ($items as $item):
              $lineTotal = $item['unit_price'] * $item['quantity'];
              $subtotal += $lineTotal;
            ?>
              <tr>
                <td><?= htmlspecialchars($item['product_name'] ?? 'Product Deleted') ?></td>
                <td><?= number_format((float)$item['unit_price'], 2) ?> JD</td>
                <td><?= (int)$item['quantity'] ?></td>
                <td><b><?= number_format($lineTotal, 2) ?> JD</b></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </div>
</main>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>