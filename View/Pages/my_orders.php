<?php
$currentPage = 'my_orders';
$pageTitle = 'My Orders — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/orders.php';

$orderController = new OrderController(new Db());

// Hardcoded customer ID for testing
$customerId = $_SESSION['customer_id'] ?? 1;

// ---------------------------------------------------------
// HANDLE USER ACTIONS (CANCEL or RECEIVED)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. USER CANCELS ORDER
    if (isset($_POST['cancel_order_id'])) {
        $cancelId = (int)$_POST['cancel_order_id'];
        $orderToCancel = $orderController->getOrderById($cancelId);
        
        if ($orderToCancel && $orderToCancel->customer_id === $customerId && $orderController->canUserCancel($orderToCancel)) {
            $orderController->updateOrderStatus($cancelId, 'cancelled');
        }
        header("Location: ../Layout/routing.php?page=my_orders");
        exit;
    }

    // 2. USER CONFIRMS RECEIPT
    if (isset($_POST['received_order_id'])) {
        $receivedId = (int)$_POST['received_order_id'];
        $orderToReceive = $orderController->getOrderById($receivedId);
        
        if ($orderToReceive && $orderToReceive->customer_id === $customerId) {
            $orderController->markAsDeliveredByCustomer($receivedId);
        }
        header("Location: ../Layout/routing.php?page=my_orders");
        exit;
    }
}

// Fetch the user's orders
$myOrders = $orderController->getOrdersByCustomer($customerId);

require_once __DIR__ . '/../Includes/header.php';
?>

<!-- Load orders.css specifically for the modal styles -->
<link rel="stylesheet" href="../css/orders.css" />

<?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    
    <!-- ADDED max-width to fix the zoomed-in look -->
    <main class="container page-main" style="max-width: 1200px; margin: 0 auto;">
      <span class="eyebrow">// ORDER HISTORY</span>
      <h1 class="page-title">My <span class="text-grad">orders.</span></h1>

      <div class="account-layout">
        <!-- Sidebar Navigation -->
        <aside class="account-nav card">
          <div class="avatar">AM</div>
          <h3>Alex Morgan</h3>
          <small>NEXORA MEMBER</small>
          <a href="../Layout/routing.php?page=account">Profile overview</a>
          <a class="active" href="../Layout/routing.php?page=my_orders">My orders</a>
          <a href="../Layout/routing.php?page=wishlist">Wishlist</a>
		            <a href="../Layout/routing.php?page=account#settings">Account settings</a>

        </aside>

        <!-- Orders Table -->
        <section>
          <article class="card profile-card">
            <div class="row-between">
              <div>
                <h2>Order History</h2>
                <p class="muted">Track, manage, and receive your orders.</p>
              </div>
            </div>

            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>ORDER</th>
                    <th>DATE</th>
                    <th>ITEMS</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($myOrders)): ?>
                      <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px;">You have no orders yet.</td></tr>
                  <?php else: foreach ($myOrders as $o): 
                      $badge = match($o->status) {
                          'delivered' => 'status-green',
                          'confirmed' => 'status-blue',
                          'shipped'   => 'status-pink',
                          'pending'   => 'status-amber',
                          'cancelled' => 'status-red',
                          default     => 'status-blue',
                      };
                  ?>
                    <tr>
                      <td>#NX-<?= $o->id ?></td>
                      <td><?= date('M d, Y - h:i A', strtotime($o->order_date)) ?></td>
                      <td><?= $o->items_count ?> items</td>
                      <td><?= number_format($o->total_amount, 2) ?> JD</td>
                      <td><span class="status <?= $badge ?>"><?= ucfirst($o->status) ?></span></td>
                      <td>
                        <!-- 1. IF SHIPPED: Show "Received" button -->
                        <?php if ($o->status === 'shipped'): ?>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openActionModal('received', <?= $o->id ?>)">Yes, Received</button>
                            
                            <form id="received-form-<?= $o->id ?>" method="POST" style="display:none;">
                                <input type="hidden" name="received_order_id" value="<?= $o->id ?>">
                            </form>
                        
                        <!-- 2. IF PENDING/CONFIRMED: Show "Cancel" if within 5 hours -->
                        <?php elseif ($orderController->canUserCancel($o)): ?>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="openActionModal('cancel', <?= $o->id ?>)" style="color: #fb7185; border-color: #fb7185;">Cancel Order</button>
                            
                            <form id="cancel-form-<?= $o->id ?>" method="POST" style="display:none;">
                                <input type="hidden" name="cancel_order_id" value="<?= $o->id ?>">
                            </form>
                        
                        <!-- 3. OTHERWISE: Show nothing -->
                        <?php else: ?>
                            <span class="muted" style="font-size: 0.75rem;">-</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </article>
        </section>
      </div>
    </main>

    <nav class="page-links" aria-label="Store pages">
      <a href="../Layout/routing.php?page=home">Home</a><a href="../Layout/routing.php?page=products">Products</a
      ><a href="../Layout/routing.php?page=cart">Cart</a><a href="../Layout/routing.php?page=checkout">Checkout</a
      ><a href="../Layout/routing.php?page=account">My account</a><a href="../Layout/routing.php?page=wishlist">Wishlist</a
      ><a href="../Layout/routing.php?page=login">Login</a><a href="../Layout/routing.php?page=register">Register</a>
    </nav>

    <!-- Custom Action Confirmation Modal -->
    <div id="action-modal" class="modal-overlay">
      <div class="modal-content">
        <h3 id="modal-title">Confirm Action</h3>
        <p id="modal-desc">Are you sure you want to proceed?</p>
        <div class="modal-actions">
          <button type="button" class="btn btn-ghost btn-sm" onclick="closeActionModal()">Go Back</button>
          <button type="button" class="btn btn-primary btn-sm" id="confirm-action-btn">Confirm</button>
        </div>
      </div>
    </div>

    <script>
    // --- Modal Logic ---
    const modal = document.getElementById('action-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const confirmBtn = document.getElementById('confirm-action-btn');
    let currentFormId = null;

    function openActionModal(actionType, orderId) {
        if (actionType === 'received') {
            modalTitle.innerText = "Confirm Receipt?";
            modalDesc.innerText = "Are you sure you have received this order?";
            confirmBtn.style.background = "var(--grad)";
            currentFormId = 'received-form-' + orderId;
        } else {
            modalTitle.innerText = "Cancel Order?";
            modalDesc.innerText = "Are you sure you want to cancel this order? This action cannot be undone.";
            confirmBtn.style.background = "#fb7185";
            currentFormId = 'cancel-form-' + orderId;
        }
        modal.style.display = 'flex'; 
    }

    function closeActionModal() {
        modal.style.display = 'none'; 
        currentFormId = null;
    }

    confirmBtn.addEventListener('click', function() {
        if (currentFormId) {
            document.getElementById(currentFormId).submit(); 
        }
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeActionModal();
        }
    });
    </script>

  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>