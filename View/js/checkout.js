// --- MAP INITIALIZATION ---
const map = L.map('map').setView([31.9539, 35.9106], 12);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
const marker = L.marker([31.9539, 35.9106], { draggable: true }).addTo(map);

setTimeout(function() { map.invalidateSize(); }, 400);

async function updateAddress(lat, lng) {
    const display = document.getElementById('address-display');
    const hiddenInput = document.getElementById('shipping_address');
    display.innerText = "Loading...";
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=jsonv2`, { headers: { 'User-Agent': 'NEXORA_Student_Project' } });
        const data = await response.json();
        if (data.display_name) {
            display.innerText = "Selected: " + data.display_name;
            hiddenInput.value = data.display_name;
        }
    } catch (error) {
        display.innerText = "Error fetching address. Try again.";
    }
}

map.on('click', function(e) { marker.setLatLng(e.latlng); updateAddress(e.latlng.lat, e.latlng.lng); });
marker.on('dragend', function(e) { const pos = marker.getLatLng(); updateAddress(pos.lat, pos.lng); });
updateAddress(31.9539, 35.9106);

// --- COUPON AJAX LOGIC ---
function updateCheckoutCoupon(coupon) {
    const summary = document.querySelector('.summary');
    const couponSection = document.getElementById('checkout-coupon-section');
    const subtotal = Number(summary.dataset.subtotal);
    const delivery = Number(summary.dataset.delivery);
    let discount = 0;

    couponSection.replaceChildren();

    if (coupon) {
        discount = subtotal >= coupon.min_order
            ? Math.min(coupon.type === 'percentage' ? subtotal * coupon.value / 100 : coupon.value, subtotal)
            : 0;

        const appliedLine = document.createElement('div');
        appliedLine.className = 'summary-line';
        appliedLine.style.cssText = 'color: var(--green); border: 1px dashed var(--green); padding: 10px; border-radius: 8px; background: rgba(57,255,136,0.05); margin-bottom: 10px;';

        const appliedLabel = document.createElement('span');
        appliedLabel.style.cssText = 'display:flex; align-items:center; gap:5px;';
        const code = document.createElement('b');
        code.textContent = coupon.code;
        appliedLabel.append(code, document.createTextNode(' Applied'));

        const discountLabel = document.createElement('b');
        discountLabel.className = 'coupon-discount';
        discountLabel.textContent = `-${discount.toFixed(2)} JD`;
        appliedLine.append(appliedLabel, discountLabel);

        const removeWrapper = document.createElement('div');
        removeWrapper.style.cssText = 'text-align: right; margin-bottom: 15px;';
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Remove Coupon';
        removeButton.style.cssText = 'background:none; border:none; color:var(--pink); font-size:0.75rem; cursor:pointer; text-decoration:underline;';
        removeButton.addEventListener('click', removeCouponAjax);
        removeWrapper.append(removeButton);
        couponSection.append(appliedLine, removeWrapper);
    } else {
        const applyButton = document.createElement('button');
        applyButton.type = 'button';
        applyButton.className = 'coupon-btn';
        applyButton.innerHTML = '<span>🎟️ Apply Coupon Code</span>';
        const availableCount = document.createElement('span');
        availableCount.style.cssText = 'font-size: 0.75rem; color: var(--muted);';
        availableCount.textContent = `${couponSection.dataset.availableCoupons} available`;
        applyButton.append(availableCount);
        applyButton.addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('coupon-modal').style.display = 'flex';
        });
        couponSection.append(applyButton);
    }

    const discountedSubtotal = subtotal - discount;
    const tax = discountedSubtotal * 0.05;
    document.getElementById('checkout-tax').textContent = `${tax.toFixed(2)} JD`;
    document.getElementById('checkout-total').textContent = `${(discountedSubtotal + tax + delivery).toFixed(2)} JD`;
    document.getElementById('btn-total').textContent = (discountedSubtotal + tax + delivery).toFixed(2);
}

function applyCouponAjax(event, id) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('apply_coupon_id', id);

    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            updateCheckoutCoupon(data.coupon);
            document.getElementById('coupon-modal').style.display = 'none';
        } else {
            alert(data.message === 'minimum_not_met'
                ? 'Your cart subtotal is below the minimum required for this coupon.'
                : 'Error applying coupon: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error applying coupon:', error);
        alert('Failed to apply coupon. Please try again.');
    });
}

function removeCouponAjax(event) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('remove_coupon', '1');

    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            updateCheckoutCoupon(null);
        }
    })
    .catch(error => {
        console.error('Error removing coupon:', error);
        alert('Failed to remove coupon. Please try again.');
    });
}

// --- CHECKOUT & RECEIPT LOGIC ---
let lastOrderData = null;

// --- PURE JAVASCRIPT PDF GENERATOR (BULLETPROOF) ---
// --- PDF RECEIPT GENERATOR (FIXED FOR ARABIC TEXT) ---
function downloadReceipt() {
    if (!lastOrderData) {
        alert("No order data found. Please place an order first.");
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Generating PDF...';

    // Helper to remove Arabic characters and clean up spacing
    function cleanText(text) {
        if (!text) return "";
        // Remove Arabic characters (Unicode range \u0600-\u06FF) and other unsupported symbols
        let cleaned = text.replace(/[\u0600-\u06FF]/g, '');
        // Remove extra commas or spaces left behind
        cleaned = cleaned.replace(/,\s*,/g, ',').replace(/\s+/g, ' ').trim();
        return cleaned;
    }

    // Initialize jsPDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'letter');

    // Header
    doc.setFont("helvetica", "bold");
    doc.setFontSize(26);
    doc.setTextColor(139, 92, 246); // NEXORA Purple
    doc.text("NEXORA", 105, 30, { align: "center" });

    doc.setFontSize(12);
    doc.setTextColor(100, 100, 100);
    doc.setFont("helvetica", "normal");
    doc.text("Build Beyond Limits", 105, 38, { align: "center" });

    // Horizontal Line
    doc.setDrawColor(200, 200, 200);
    doc.line(20, 45, 190, 45);

    // Customer Details
    doc.setFontSize(10);
    doc.setTextColor(100, 100, 100);
    doc.text("BILLED TO:", 20, 60);
    doc.text("RECEIPT #:", 140, 60);

    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.setFont("helvetica", "bold");
    doc.text(document.getElementById('full_name').value, 20, 68);
    doc.text("#" + lastOrderData.order_id, 140, 68);

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.setTextColor(100, 100, 100);

    // Clean the address before printing
    const safeAddress = cleanText(lastOrderData.address);
    
    // Split address into multiple lines if it's too long
    const splitAddress = doc.splitTextToSize(safeAddress, 100);
    doc.text(splitAddress, 20, 74);
    
    doc.text(lastOrderData.date, 140, 74);

    // Table Header
    doc.setFillColor(248, 245, 255);
    doc.rect(20, 95, 170, 10, "F");
    doc.setFont("helvetica", "bold");
    doc.setTextColor(0, 0, 0);
    doc.text("DESCRIPTION", 25, 101);
    doc.text("AMOUNT", 170, 101, { align: "right" });

    // Table Row
    doc.setFont("helvetica", "normal");
    doc.text("Order Total (Cash on Delivery)", 25, 115);
    doc.setFont("helvetica", "bold");
    doc.text(lastOrderData.total + " JD", 170, 115, { align: "right" });

    // Total Line
    doc.setDrawColor(200, 200, 200);
    doc.line(20, 120, 190, 120);

    // Footer
    doc.setFont("helvetica", "normal");
    doc.setTextColor(150, 150, 150);
    doc.setFontSize(10);
    doc.text("Thank you for shopping with NEXORA!", 105, 140, { align: "center" });
    doc.text("For support, contact us at support@nexora.tech", 105, 146, { align: "center" });

    // Save the PDF
    doc.save(`NEXORA_Receipt_${lastOrderData.order_id}.pdf`);
    
    // Reset button text
    btn.innerHTML = originalText;
}

// --- EVENT LISTENERS ---
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('manual-coupon-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('manual-coupon-input').value.trim();
        if(!code) return;

        const formData = new FormData();
        formData.append('coupon_code', code);

        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                updateCheckoutCoupon(data.coupon);
                document.getElementById('coupon-modal').style.display = 'none';
            } else {
                alert(data.message === 'minimum_not_met'
                    ? 'Your cart subtotal is below the minimum required for this coupon.'
                    : 'Invalid coupon: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error applying coupon:', error);
            alert('Failed to apply coupon. Please try again.');
        });
    });

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('confirm-order-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Processing...';
        btn.disabled = true;

        const formData = new FormData(this);
        fetch('../Pages/checkout_process.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                lastOrderData = data;
                document.getElementById('modal-order-id').innerText = '#' + data.order_id;
                document.getElementById('modal-order-total').innerText = data.total + ' JD';
                document.getElementById('success-modal').style.display = 'flex';
                document.querySelector('.summary').style.opacity = '0.5';
            } else {
                alert('Something went wrong: ' + (data.message || 'Unknown error'));
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to process order. Please check console.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
});