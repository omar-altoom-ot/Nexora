<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CartController.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';
require_once __DIR__ . '/../../Model/Customer.php';

$db = new Db();
$cartController = new CartController($db);
$couponController = new CouponController($db);
$customerModel = new Customer($db);

$customerId = (int)($_SESSION['customer_id'] ?? 0);
$isLoggedIn = isset($_SESSION['customer_id']);

$customerData = null;
if ($isLoggedIn) {
    $customerData = $customerModel->getCustomerById($customerId);
}
$fullName = $customerData ? trim($customerData['first_name'] . ' ' . $customerData['last_name']) : '';
$savedPhone = $customerData['phone'] ?? '';
$phoneRequired = empty($savedPhone) ? 'required' : '';

// 1. HANDLE COUPON ACTIONS (AJAX FALLBACK)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['apply_coupon_id']) || isset($_POST['coupon_code']))) {
    $coupon = null;
    $error = '';

    if (isset($_POST['coupon_code']) && trim($_POST['coupon_code']) !== '') {
        $coupon = $couponController->getByCode(trim($_POST['coupon_code']));
        if (!$coupon) $error = 'invalid';
    } elseif (isset($_POST['apply_coupon_id'])) {
        $coupon = $couponController->getById((int)$_POST['apply_coupon_id']);
    }

    if ($coupon) {
        if ($coupon->is_active && strtotime($coupon->valid_until) >= strtotime(date('Y-m-d')) && $coupon->used_count < $coupon->usage_limit) {
            $couponSubtotal = 0;
            foreach ($cartController->getItems($customerId) as $couponCartItem) {
                $couponSubtotal += $couponCartItem['price'] * $couponCartItem['quantity'];
            }

            if ($couponSubtotal < $coupon->min_order_amount) {
                $error = 'minimum_not_met';
            } else {
                $_SESSION['applied_coupon'] = [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->discount_type,
                    'value' => $coupon->discount_value,
                    'min_order' => $coupon->min_order_amount
                ];
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['status' => 'success', 'coupon' => ['code' => $coupon->code, 'type' => $coupon->discount_type, 'value' => (float)$coupon->discount_value, 'min_order' => (float)$coupon->min_order_amount]]);
                    exit;
                }
                header("Location: ../Layout/routing.php?page=checkout");
                exit;
            }
        } else {
            $error = 'expired';
        }
    }
    if ($error) {
        $_SESSION['coupon_error'] = $error;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => $error]);
            exit;
        }
        header("Location: ../Layout/routing.php?page=checkout&show_coupon=1");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'success']);
        exit;
    }
    header("Location: ../Layout/routing.php?page=checkout");
    exit;
}

// 2. FETCH CART ITEMS
$cartItems = $cartController->getItems($customerId);

// 3. CALCULATE TOTALS
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}

$discountAmount = 0;
$appliedCoupon = $_SESSION['applied_coupon'] ?? null;

if ($appliedCoupon) {
    if ($subtotal >= $appliedCoupon['min_order']) {
        if ($appliedCoupon['type'] === 'percentage') {
            $discountAmount = $subtotal * ($appliedCoupon['value'] / 100);
        } else {
            $discountAmount = $appliedCoupon['value'];
        }
        $discountAmount = min($discountAmount, $subtotal);
    } else {
        unset($_SESSION['applied_coupon']);
        $appliedCoupon = null;
        $_SESSION['coupon_error'] = 'minimum_not_met';
    }
}

$discountedSubtotal = $subtotal - $discountAmount;
$tax = $discountedSubtotal * 0.05;
$delivery = empty($cartItems) ? 0 : 2.00;
$totalDue = $discountedSubtotal + $tax + $delivery;

$userCoupons = [];
if ($isLoggedIn) {
    $userCoupons = $couponController->getUserCoupons($customerId);
}

$couponError = $_SESSION['coupon_error'] ?? '';
unset($_SESSION['coupon_error']);

$currentPage = 'checkout';
$pageTitle = 'Checkout — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../Includes/header.php';
?>
<link rel="stylesheet" href="../css/orders.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    #map { height: 300px; width: 100% !important; border-radius: 10px; border: 1px solid var(--line); margin-bottom: 15px; z-index: 10; position: relative; background: #1a1a1a; }
    .leaflet-container { background: #000 !important; z-index: 1 !important; }
    .coupon-btn { background: rgba(139, 92, 246, 0.1); border: 1px dashed var(--purple); color: var(--cyan); padding: 10px; width: 100%; text-align: left; border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    .coupon-btn:hover { background: rgba(139, 92, 246, 0.2); border-color: var(--cyan); }
    .req { color: var(--pink); margin-left: 4px; }
</style>

<?php require __DIR__ . '/../Layout/user_nav.php'; ?>
<main class="container page-main narrow">
  <span class="eyebrow">// FINAL CHECKPOINT</span>
  <h1 class="page-title">Confirm your <span class="text-grad">order.</span></h1>
  <div class="checkout-layout">

    <form class="card checkout-form" id="checkout-form">
      <?php foreach ($cartItems as $item): ?>
          <input type="hidden" name="cart_items[]" value="<?= $item['id'] ?>|<?= $item['quantity'] ?>|<?= $item['price'] ?>">
      <?php endforeach; ?>

      <h2>Delivery details</h2>
      <p class="muted">Where should we deliver your gear?</p>

      <label class="field">
        <span>Full name</span>
        <input class="input" name="full_name" id="full_name" value="<?= htmlspecialchars($fullName) ?>" readonly style="opacity: 0.8; cursor: not-allowed;" required />
      </label>

      <label class="field">
        <span>Phone number <?php if ($phoneRequired): ?><span class="req">*</span><?php endif; ?></span>
        <input class="input" name="phone" id="phone" type="tel" placeholder="+962 7X XXX XXXX" value="<?= htmlspecialchars($savedPhone) ?>" <?= $phoneRequired ?> />
        <?php if (empty($savedPhone)): ?>
            <small class="muted" style="margin-top: 5px; display: block; color: var(--pink);">Please provide a phone number for delivery.</small>
        <?php endif; ?>
      </label>

      <label class="field">
        <span>Delivery Location (Click on map) <span class="req">*</span></span>
        <div id="map"></div>
        <input type="hidden" name="shipping_address" id="shipping_address" required>
        <small class="muted" id="address-display" style="margin-top: 5px; display: block;">No location selected yet.</small>
      </label>

      <div class="notice">Cash on delivery.</div>

      <button class="btn btn-primary full" type="submit" id="confirm-order-btn" <?= empty($cartItems) ? 'disabled' : '' ?>>
        Confirm order · <span id="btn-total"><?= number_format($totalDue, 2) ?></span> JD
      </button>
    </form>

    <aside class="summary card" data-subtotal="<?= htmlspecialchars((string)$subtotal, ENT_QUOTES, 'UTF-8') ?>" data-delivery="<?= htmlspecialchars((string)$delivery, ENT_QUOTES, 'UTF-8') ?>">
      <h2>Order details</h2>

      <?php if (empty($cartItems)): ?>
          <p class="muted">Your cart is empty. <a href="../Layout/routing.php?page=cart" style="color:var(--cyan)">Go back to cart</a></p>
      <?php else: ?>
          <?php foreach ($cartItems as $item): ?>
              <div class="summary-line">
                  <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                  <b><?= number_format($item['price'] * $item['quantity'], 2) ?> JD</b>
              </div>
          <?php endforeach; ?>

          <div class="summary-line" style="margin-top: 10px; border-top: 1px solid var(--line); padding-top: 10px;">
              <span>Subtotal</span><b><?= number_format($subtotal, 2) ?> JD</b>
          </div>

          <!-- COUPON SECTION -->
          <div id="checkout-coupon-section" data-available-coupons="<?= count($userCoupons) ?>">
          <?php if ($isLoggedIn): ?>
              <?php if ($appliedCoupon): ?>
                  <div class="summary-line" style="color: var(--green); border: 1px dashed var(--green); padding: 10px; border-radius: 8px; background: rgba(57,255,136,0.05); margin-bottom: 10px;">
                      <span style="display:flex; align-items:center; gap:5px;">
                          <b><?= htmlspecialchars($appliedCoupon['code']) ?></b> Applied
                      </span>
                      <b class="coupon-discount">-<?= number_format($discountAmount, 2) ?> JD</b>
                  </div>
                  <div style="text-align: right; margin-bottom: 15px;">
                      <button type="button" onclick="removeCouponAjax(event)" style="background:none; border:none; color:var(--pink); font-size:0.75rem; cursor:pointer; text-decoration:underline;">Remove Coupon</button>
                  </div>
              <?php else: ?>
                  <button type="button" class="coupon-btn" onclick="document.getElementById('coupon-modal').style.display='flex'">
                      <span>🎟️ Apply Coupon Code</span>
                      <span style="font-size: 0.75rem; color: var(--muted);"><?= count($userCoupons) ?> available</span>
                  </button>
              <?php endif; ?>
          <?php else: ?>
              <div class="summary-line" style="font-size: 0.8rem; color: var(--muted); margin-bottom: 15px;">
                  <a href="../Layout/routing.php?page=login" style="color: var(--cyan);">Log in</a> to apply coupons.
              </div>
          <?php endif; ?>
          </div>
          <!-- END COUPON SECTION -->

          <div class="summary-line"><span>Tax · 5%</span><b id="checkout-tax"><?= number_format($tax, 2) ?> JD</b></div>
          <div class="summary-line"><span>Delivery</span><b><?= number_format($delivery, 2) ?> JD</b></div>
          <div class="summary-total"><span>Total due</span><b id="checkout-total"><?= number_format($totalDue, 2) ?> JD</b></div>
      <?php endif; ?>
    </aside>
  </div>
</main>

<!-- COUPON MODAL -->
<div id="coupon-modal" class="modal-overlay" style="<?= isset($_GET['show_coupon']) ? 'display: flex;' : '' ?>">
  <div class="modal-content" style="max-height: 85vh; display: flex; flex-direction: column;">
    <h3>Select a Coupon</h3>
    <p class="muted" style="margin-bottom: 15px;">Apply an available coupon or paste a specific code.</p>

    <form id="manual-coupon-form" style="display: flex; gap: 8px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px dashed var(--line);">
        <input type="text" name="coupon_code" id="manual-coupon-input" class="input" placeholder="Enter promo code" required style="padding: 10px;">
        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0 15px;">Apply</button>
    </form>

    <?php if ($couponError): ?>
        <div style="background: rgba(251, 113, 133, 0.1); border: 1px solid #fb7185; color: #fb7185; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; text-align: center;">
            <?php if ($couponError === 'invalid'): ?>
                Invalid coupon code. Please check and try again.
            <?php elseif ($couponError === 'expired'): ?>
                This coupon is expired or has reached its usage limit.
            <?php elseif ($couponError === 'minimum_not_met'): ?>
                Your cart subtotal is below the minimum required for this coupon.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div style="overflow-y: auto; flex: 1; padding-right: 5px;">
        <p class="muted" style="font-size: 0.8rem; margin-bottom: 10px;">Available Coupons:</p>

        <?php if (empty($userCoupons)): ?>
            <div style="text-align: center; padding: 10px;">
                <p class="muted">You don't have any active coupons.</p>
            </div>
        <?php else: ?>
            <?php foreach (array_slice($userCoupons, 0, 4) as $c): ?>
                <button type="button" onclick="applyCouponAjax(event, <?= $c->id ?>)" class="card" style="width: 100%; text-align: left; padding: 15px; margin-bottom: 10px; background: rgba(139,92,246,0.05); border: 1px solid var(--line); cursor: pointer; transition: background 0.2s;">
                    <div style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                        <b style="color: var(--cyan); font-family: var(--font-head); font-size: 1.1rem;"><?= htmlspecialchars($c->code) ?></b>
                        <b style="color: var(--green);"><?= $c->discount_type === 'percentage' ? $c->discount_value . '% OFF' : $c->discount_value . ' JD OFF' ?></b>
                    </div>
                    <small class="muted">Min. spend: <?= number_format($c->min_order_amount, 2) ?> JD</small>
                </button>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal-actions" style="margin-top: 15px;">
      <button type="button" class="btn btn-ghost btn-sm full" onclick="document.getElementById('coupon-modal').style.display='none'">Close</button>
    </div>
  </div>
</div>

<!-- SUCCESS MODAL -->
<div id="success-modal" class="modal-overlay" style="z-index: 10000;">
  <div class="modal-content" style="text-align: center; border-color: var(--green); box-shadow: 0 0 40px rgba(57,255,136,0.2);">
    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(57,255,136,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
    </div>
    <h3 style="color: var(--green); font-size: 1.5rem; margin-bottom: 10px;">Order Placed!</h3>
    <p class="muted" style="margin-bottom: 20px;">Your order <b id="modal-order-id">#---</b> has been placed successfully.</p>

    <div style="background: var(--bg-2); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: left; font-size: 0.85rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span class="muted">Total Amount:</span><b style="color: #fff;" id="modal-order-total">0.00 JD</b>
        </div>
    </div>

    <!-- CHANGED: Download Receipt Button -->
    <button type="button" onclick="downloadReceipt()" class="btn btn-ghost full" style="margin-bottom: 10px; border-color: var(--cyan); color: var(--cyan);">
        ⬇ Download PDF Receipt
    </button>
    <a href="../Layout/routing.php?page=products" class="btn btn-primary full">Continue Shopping</a>
  </div>
</div>

<!-- HIDDEN RECEIPT TEMPLATE FOR PDF (CHANGED: position absolute instead of display none) -->
<div id="receipt-template" style="position: absolute; left: -9999px; top: 0; padding: 40px; color: #333; font-family: Arial, sans-serif; background: #fff; width: 800px;">
    <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #8b5cf6; padding-bottom: 20px;">
        <h1 style="margin: 0; font-size: 36px; color: #8b5cf6; letter-spacing: 2px;">NEXORA</h1>
        <p style="margin: 5px 0 0; font-size: 14px; color: #666;">Build Beyond Limits</p>
    </div>
    
    <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
        <div>
            <p style="margin: 0; font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Billed To:</p>
            <strong style="font-size: 16px; color: #000; display: block; margin-top: 5px;" id="pdf-customer-name"></strong>
            <p style="margin: 5px 0 0; font-size: 14px; color: #333;" id="pdf-customer-address"></p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Receipt #:</p>
            <strong style="font-size: 16px; color: #000; display: block; margin-top: 5px;" id="pdf-order-id"></strong>
            <p style="margin: 5px 0 0; font-size: 14px; color: #333;" id="pdf-order-date"></p>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
        <thead>
            <tr style="background: #f8f5ff; text-align: left; color: #333; font-size: 14px;">
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Description</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 15px 12px; font-size: 14px; color: #333; border-bottom: 1px solid #eee;">Order Total (Cash on Delivery)</td>
                <td style="padding: 15px 12px; font-size: 16px; font-weight: bold; text-align: right; color: #000; border-bottom: 1px solid #eee;" id="pdf-order-total"></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 60px; border-top: 2px solid #eee; padding-top: 20px;">
        <p style="margin: 0; font-size: 14px; color: #666;">Thank you for shopping with NEXORA!</p>
        <p style="margin: 5px 0 0; font-size: 12px; color: #999;">For support, contact us at support@nexora.tech</p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- ADDED: jsPDF Library specifically for drawing the receipt -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- ADDED: Link to the external JS file -->
<script src="../js/checkout.js"></script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>