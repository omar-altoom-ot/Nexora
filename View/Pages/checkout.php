<?php
// 1. HANDLE COUPON ACTIONS (APPLY / REMOVE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['apply_coupon_id']) || isset($_POST['coupon_code']))) {
    require_once __DIR__ . '/../../Config/DBconnection.php';
    require_once __DIR__ . '/../../Controllers/CouponController.php';

    $couponController = new CouponController(new Db());
    $coupon = null;
    $error = '';

    if (isset($_POST['coupon_code']) && trim($_POST['coupon_code']) !== '') {
        // Manual Code Entry
        $coupon = $couponController->getByCode(trim($_POST['coupon_code']));
        if (!$coupon) {
            $error = 'invalid';
        }
    } elseif (isset($_POST['apply_coupon_id'])) {
        // Selected from list
        $coupon = $couponController->getById((int)$_POST['apply_coupon_id']);
    }

    // Validate the coupon if found
    if ($coupon) {
        if ($coupon->is_active && strtotime($coupon->valid_until) >= strtotime(date('Y-m-d')) && $coupon->used_count < $coupon->usage_limit) {
            $_SESSION['applied_coupon'] = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->discount_type,
                'value' => $coupon->discount_value,
                'min_order' => $coupon->min_order_amount
            ];
            header("Location: ../Layout/routing.php?page=checkout");
            exit;
        } else {
            $error = 'expired';
        }
    }

    // If there's an error, save it to session to display in the modal
    if ($error) {
        $_SESSION['coupon_error'] = $error;
        header("Location: ../Layout/routing.php?page=checkout&show_coupon=1");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    header("Location: ../Layout/routing.php?page=checkout");
    exit;
}

// 2. SETUP PAGE VARIABLES
$currentPage = 'checkout';
$pageTitle = 'Checkout — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';
require_once __DIR__ . '/../Includes/header.php';

$db = new Db();
$customerId = $_SESSION['customer_id'] ?? 1;
$isLoggedIn = isset($_SESSION['customer_id']); 

// Fetch Cart Data
$cartSql = "
    SELECT c.id AS cart_id, ci.product_id, ci.quantity, p.name, p.price
    FROM carts c
    JOIN cart_items ci ON c.id = ci.cart_id
    JOIN products p ON p.id = ci.product_id
    WHERE c.customer_id = ?
";
$cartItems = $db->run_query($cartSql, [$customerId])->fetchAll();

// Calculate Totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}

// Coupon Logic
$discountAmount = 0;
$appliedCoupon = $_SESSION['applied_coupon'] ?? null;

if ($appliedCoupon) {
    // Check if cart still meets min order requirement
    if ($subtotal >= $appliedCoupon['min_order']) {
        if ($appliedCoupon['type'] === 'percentage') {
            $discountAmount = $subtotal * ($appliedCoupon['value'] / 100);
        } else {
            $discountAmount = $appliedCoupon['value'];
        }
        $discountAmount = min($discountAmount, $subtotal);
    } else {
        // Cart dropped below minimum, remove coupon
        unset($_SESSION['applied_coupon']);
        $appliedCoupon = null;
        $_SESSION['coupon_error'] = 'minimum_not_met';
    }
}

$discountedSubtotal = $subtotal - $discountAmount;
$tax = $discountedSubtotal * 0.05;
$delivery = 2.00;
$totalDue = $discountedSubtotal + $tax + $delivery;

// Fetch User's Coupons (only if logged in)
$userCoupons = [];
if ($isLoggedIn) {
    $couponController = new CouponController(new Db());
    $userCoupons = $couponController->getUserCoupons($customerId);
}

// Check for error messages
$couponError = $_SESSION['coupon_error'] ?? '';
unset($_SESSION['coupon_error']);
?>
<link rel="stylesheet" href="../css/orders.css" />

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map { height: 300px; width: 100% !important; border-radius: 10px; border: 1px solid var(--line); margin-bottom: 15px; z-index: 10; position: relative; background: #1a1a1a; }
    .leaflet-container { background: #000 !important; z-index: 1 !important; }
    .coupon-btn { background: rgba(139, 92, 246, 0.1); border: 1px dashed var(--purple); color: var(--cyan); padding: 10px; width: 100%; text-align: left; border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    .coupon-btn:hover { background: rgba(139, 92, 246, 0.2); border-color: var(--cyan); }
</style>

<?php require __DIR__ . '/../Layout/user_nav.php'; ?>
<main class="container page-main narrow">
  <span class="eyebrow">// FINAL CHECKPOINT</span>
  <h1 class="page-title">Confirm your <span class="text-grad">order.</span></h1>
  <div class="checkout-layout">

    <form class="card checkout-form" id="checkout-form" method="POST" action="../Pages/checkout_process.php">
      <?php foreach ($cartItems as $item): ?>
          <input type="hidden" name="cart_items[]" value="<?= $item['product_id'] ?>|<?= $item['quantity'] ?>|<?= $item['price'] ?>">
      <?php endforeach; ?>

      <h2>Delivery details</h2>
      <p class="muted">Where should we deliver your gear?</p>

      <label class="field">
        <span>Full name</span>
        <input class="input" name="full_name" placeholder="Your full name" required />
      </label>

      <label class="field">
        <span>Delivery Location (Click on map)</span>
        <div id="map"></div>
        <input type="hidden" name="shipping_address" id="shipping_address" required>
        <small class="muted" id="address-display" style="margin-top: 5px; display: block;">No location selected yet.</small>
      </label>

      <label class="field">
        <span>Phone number</span>
        <input class="input" name="phone" type="tel" placeholder="+962 7X XXX XXXX" required />
      </label>

      <div class="notice">Cash on delivery.</div>

      <button class="btn btn-primary full" type="button" id="confirm-order-btn">
        Confirm order · <span id="btn-total"><?= number_format($totalDue, 2) ?></span> JD
      </button>
    </form>

    <aside class="summary card">
      <h2>Order details</h2>
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
      <?php if ($isLoggedIn): ?>
          <?php if ($appliedCoupon): ?>
              <div class="summary-line" style="color: var(--green); border: 1px dashed var(--green); padding: 10px; border-radius: 8px; background: rgba(57,255,136,0.05); margin-bottom: 10px;">
                  <span style="display:flex; align-items:center; gap:5px;">
                      <b><?= htmlspecialchars($appliedCoupon['code']) ?></b> Applied
                  </span>
                  <b>-<?= number_format($discountAmount, 2) ?> JD</b>
              </div>
              <form method="POST" action="../Layout/routing.php?page=checkout" style="text-align: right; margin-bottom: 15px;">
                  <input type="hidden" name="remove_coupon" value="1">
                  <button type="submit" style="background:none; border:none; color:var(--pink); font-size:0.75rem; cursor:pointer; text-decoration:underline;">Remove Coupon</button>
              </form>
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
      <!-- END COUPON SECTION -->

      <div class="summary-line"><span>Tax · 5%</span><b><?= number_format($tax, 2) ?> JD</b></div>
      <div class="summary-line"><span>Delivery</span><b><?= number_format($delivery, 2) ?> JD</b></div>
      <div class="summary-total"><span>Total due</span><b><?= number_format($totalDue, 2) ?> JD</b></div>
    </aside>
  </div>
</main>

<!-- 3. THE COUPON MODAL (With Manual Input & Available List) -->
<div id="coupon-modal" class="modal-overlay" style="<?= isset($_GET['show_coupon']) ? 'display: flex;' : '' ?>">
  <div class="modal-content" style="max-height: 85vh; display: flex; flex-direction: column;">
    <h3>Select a Coupon</h3>
    <p class="muted" style="margin-bottom: 15px;">Apply an available coupon or paste a specific code.</p>

    <!-- MANUAL CODE INPUT -->
    <form method="POST" action="../Layout/routing.php?page=checkout" style="display: flex; gap: 8px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px dashed var(--line);">
        <input type="text" name="coupon_code" class="input" placeholder="Enter promo code" required style="padding: 10px;">
        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0 15px;">Apply</button>
    </form>

    <!-- ERROR MESSAGE -->
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
            <?php foreach ($userCoupons as $c): ?>
                <form method="POST" action="../Layout/routing.php?page=checkout">
                    <input type="hidden" name="apply_coupon_id" value="<?= $c->id ?>">
                    <button type="submit" class="card" style="width: 100%; text-align: left; padding: 15px; margin-bottom: 10px; background: rgba(139,92,246,0.05); border: 1px solid var(--line); cursor: pointer; transition: background 0.2s;">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                            <b style="color: var(--cyan); font-family: var(--font-head); font-size: 1.1rem;"><?= htmlspecialchars($c->code) ?></b>
                            <b style="color: var(--green);"><?= $c->discount_type === 'percentage' ? $c->discount_value . '% OFF' : $c->discount_value . ' JD OFF' ?></b>
                        </div>
                        <small class="muted">Min. spend: <?= number_format($c->min_order_amount, 2) ?> JD</small>
                    </button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal-actions" style="margin-top: 15px;">
      <button type="button" class="btn btn-ghost btn-sm full" onclick="document.getElementById('coupon-modal').style.display='none'">Close</button>
    </div>
  </div>
</div>

<!-- SUCCESS MODAL (Unchanged) -->
<div id="success-modal" class="modal-overlay">
  <!-- ... Keep your existing success modal code here ... -->
</div>

<script>
    // Initialize the map
    const map = L.map('map').setView([31.9539, 35.9106], 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([31.9539, 35.9106], { draggable: true }).addTo(map);

    setTimeout(function() {
        map.invalidateSize();
    }, 400);

    async function updateAddress(lat, lng) {
        const display = document.getElementById('address-display');
        const hiddenInput = document.getElementById('shipping_address');
        display.innerText = "Loading...";
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=jsonv2`, {
                headers: { 'User-Agent': 'NEXORA_Student_Project' }
            });
            const data = await response.json();
            if (data.display_name) {
                display.innerText = "Selected: " + data.display_name;
                hiddenInput.value = data.display_name;
            }
        } catch (error) {
            display.innerText = "Error fetching address. Try again.";
        }
    }

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateAddress(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        updateAddress(pos.lat, pos.lng);
    });

    updateAddress(31.9539, 35.9106);
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
