<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$currentPage = 'checkout';
$pageTitle = 'Checkout — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CartController.php';

$db = new Db();
$customerId = (int) ($_SESSION['customer_id'] ?? 0);
if ($customerId <= 0 || ($_SESSION['user_role'] ?? '') !== 'customer') {
    header('Location: ../Layout/routing.php?page=login');
    exit;
}
if (empty($_SESSION['checkout_csrf_token'])) {
    $_SESSION['checkout_csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../Includes/header.php';

// The cart belongs to this browser session, regardless of login status.
$cartItems = (new CartController($db))->getItems($customerId);

// Calculate Totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$tax = $subtotal * 0.05;
$delivery = 2.00;
$totalDue = $subtotal + $tax + $delivery;
?>
<link rel="stylesheet" href="../css/orders.css" />

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map { height: 300px; width: 100%; border-radius: 10px; border: 1px solid var(--line); margin-bottom: 15px; z-index: 1; }
</style>

    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main narrow">
      <span class="eyebrow">// FINAL CHECKPOINT</span>
      <h1 class="page-title">Confirm your <span class="text-grad">order.</span></h1>
      <div class="checkout-layout">
        
        <!-- <form class="card checkout-form" id="checkout-form"> -->
			<form class="card checkout-form"
      id="checkout-form"
      method="POST"
      action="../Pages/checkout_process.php">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['checkout_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
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
            Confirm order · <?= number_format($totalDue, 2) ?> JD
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
          <div class="summary-line"><span>Tax · 5%</span><b><?= number_format($tax, 2) ?> JD</b></div>
          <div class="summary-line"><span>Delivery</span><b><?= number_format($delivery, 2) ?> JD</b></div>
          <div class="summary-total"><span>Total due</span><b><?= number_format($totalDue, 2) ?> JD</b></div>
        </aside>
      </div>
    </main>
    
    <!-- SUCCESS MODAL -->
    <div id="success-modal" class="modal-overlay">
      <div class="modal-content" style="text-align: center;">
        <div style="font-size: 3rem; color: var(--green); margin-bottom: 10px;">✓</div>
        <h3 style="color: var(--green);">Order Confirmed!</h3>
        <p>Your order has been placed successfully.</p>
        <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: left; border: 1px solid var(--line);">
            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                <small class="muted">Order ID:</small> <b id="modal-order-id">#NX-</b>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                <small class="muted">Total:</small> <b id="modal-order-total"></b>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <small class="muted">Delivery To:</small> <b id="modal-order-address" style="text-align:right; max-width: 60%; font-size: 0.8rem;"></b>
            </div>
        </div>
        <div class="modal-actions" style="flex-direction: column; gap: 10px;">
          <button type="button" class="btn btn-ghost btn-sm full" onclick="downloadReceipt()">↓ Download Receipt (CSV)</button>
          <a href="../Layout/routing.php?page=my_orders" class="btn btn-primary btn-sm full">Go to My Orders</a>
        </div>
      </div>
    </div>

    <!-- MAP SCRIPT -->
    <script>
        const map = L.map('map').setView([31.9539, 35.9106], 12);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        const marker = L.marker([31.9539, 35.9106], { draggable: true }).addTo(map);

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
                display.innerText = "Error fetching address.";
            }
        }

        map.on('click', function(e) { marker.setLatLng(e.latlng); updateAddress(e.latlng.lat, e.latlng.lng); });
        marker.on('dragend', function(e) { const pos = marker.getLatLng(); updateAddress(pos.lat, pos.lng); });
        updateAddress(31.9539, 35.9106);
    </script>

    <!-- AJAX + MODAL SCRIPT -->
    <script>
    document.getElementById('confirm-order-btn').addEventListener('click', async function() {
        const form = document.getElementById('checkout-form');
        if(!document.getElementById('shipping_address').value) {
            alert("Please select a delivery location on the map first!");
            return;
        }
        
        const btn = this;
        btn.innerText = "Processing...";
        btn.disabled = true;
        
        try {
            // FIX: Correct path to the process file
            const response = await fetch('../Pages/checkout_process.php', {
                method: 'POST',
                body: new FormData(form)
            });
            const data = await response.json();

            if(data.status === 'success') {
                document.getElementById('modal-order-id').innerText = '#NX-' + data.order_id;
                document.getElementById('modal-order-total').innerText = data.total + ' JD';
                document.getElementById('modal-order-address').innerText = data.address;
                document.getElementById('success-modal').style.display = 'flex';
                window.lastOrder = data;
            } else {
                alert(data.message || "Something went wrong.");
                btn.innerText = "Confirm order";
                btn.disabled = false;
            }
        } catch (error) {
            console.error(error);
            alert("Network error. Please try again.");
            btn.innerText = "Confirm order";
            btn.disabled = false;
        }
    });

    function downloadReceipt() {
        if(!window.lastOrder) return;
        const data = window.lastOrder;
        let csvContent = "data:text/csv;charset=utf-8,NEXORA RECEIPT\nOrder ID,Total,Date,Address\n";
        csvContent += `"#NX-${data.order_id}","${data.total} JD","${data.date}","${data.address}"\n`;
        
        const link = document.createElement("a");
        link.setAttribute("href", encodeURI(csvContent));
        link.setAttribute("download", `NEXORA_Receipt_${data.order_id}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    </script>

  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
