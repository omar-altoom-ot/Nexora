<?php
session_start();

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/orders.php';
require_once __DIR__ . '/../../Controllers/OrderItemController.php';
require_once __DIR__ . '/../../Controllers/CouponController.php'; // ADDED

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_SESSION['customer_id'] ?? 1;
    $address = !empty($_POST['shipping_address']) ? $_POST['shipping_address'] : 'No address provided';

    // 1. Calculate Subtotal from cart
    $subtotal = 0;
    if (isset($_POST['cart_items']) && is_array($_POST['cart_items'])) {
        foreach ($_POST['cart_items'] as $itemString) {
            list($pid, $qty, $price) = explode('|', $itemString);
            $subtotal += ($qty * $price);
        }
    }

    // 2. Apply Coupon Logic
    $discountAmount = 0;
    $couponId = null;
    
    if (isset($_SESSION['applied_coupon'])) {
        $couponId = $_SESSION['applied_coupon']['id'];
        $couponController = new CouponController(new Db());
        $coupon = $couponController->getById($couponId);
        
        // Double check it's still valid
        if ($coupon && $coupon->is_active && strtotime($coupon->valid_until) >= strtotime(date('Y-m-d'))) {
            if ($subtotal >= $coupon->min_order_amount) {
                if ($coupon->discount_type === 'percentage') {
                    $discountAmount = $subtotal * ($coupon->discount_value / 100);
                } else {
                    $discountAmount = $coupon->discount_value;
                }
            }
        }
    }

    // Ensure discount doesn't exceed subtotal
    $discountAmount = min($discountAmount, $subtotal);
    $discountedSubtotal = $subtotal - $discountAmount;

    // 3. Calculate Tax and Delivery (Tax applied AFTER discount)
    $tax = $discountedSubtotal * 0.05;
    $delivery = 2.00;
    $totalAmount = (string)($discountedSubtotal + $tax + $delivery);

    // 4. Create Order
    $orderController = new OrderController(new Db());
    $orderId = $orderController->create($customerId, $totalAmount, 'pending', $address);

    // 5. Create Order Items
    $itemController = new OrderItemController(new Db());
    if (isset($_POST['cart_items']) && is_array($_POST['cart_items'])) {
        foreach ($_POST['cart_items'] as $itemString) {
            list($pid, $qty, $price) = explode('|', $itemString);
            $itemController->create($orderId, (int)$pid, (int)$qty, (float)$price);
        }
    }

    // 6. Mark Coupon as used
if ($couponId) {
        $db = new Db();
        $db->run_query("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$couponId]);
    }

    // 7. Clear the cart and applied coupon
    unset($_SESSION['cart']);
    unset($_SESSION['applied_coupon']);

    // 8. Return JSON
    echo json_encode([
        'status' => 'success',
        'order_id' => $orderId,
        'total' => number_format((float)$totalAmount, 2),
        'address' => $address,
        'date' => date('M d, Y - h:i A')
    ]);
    exit;
}
?>