<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/orders.php';
require_once __DIR__ . '/../../Controllers/OrderItemController.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';
require_once __DIR__ . '/../../Controllers/CartController.php';
require_once __DIR__ . '/../../Model/Customer.php'; // ADDED: To save new phone

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : 1;

    if ($customerId === 0) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to checkout.']);
        exit;
    }

    $address = !empty($_POST['shipping_address']) ? $_POST['shipping_address'] : 'No address provided';
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : '';

    // Check if we need to save the new phone number
    if (!empty($phone)) {
        $customerModel = new Customer(new Db());
        $customerData = $customerModel->getCustomerById($customerId);
        if ($customerData && empty($customerData['phone'])) {
            // Automatically save the phone number to their profile
            $customerModel->updateCustomer(
                $customerId,
                $customerData['first_name'],
                $customerData['last_name'],
                $customerData['email'],
                $phone
            );
        }
    }

    // 1. Calculate Subtotal
    $subtotal = 0;
    $orderItemsData = [];
    if (isset($_POST['cart_items']) && is_array($_POST['cart_items'])) {
        foreach ($_POST['cart_items'] as $itemString) {
            list($pid, $qty, $price) = explode('|', $itemString);
            $subtotal += ($qty * $price);
            $orderItemsData[] = ['pid' => (int)$pid, 'qty' => (int)$qty, 'price' => (float)$price];
        }
    }

    // 2. Apply Coupon Logic
    $discountAmount = 0;
    $couponId = null;

    if (isset($_SESSION['applied_coupon'])) {
        $couponId = $_SESSION['applied_coupon']['id'];
        $couponController = new CouponController(new Db());
        $coupon = $couponController->getById($couponId);

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

    $discountAmount = min($discountAmount, $subtotal);
    $discountedSubtotal = $subtotal - $discountAmount;

    // 3. Calculate Tax and Delivery
    $tax = $discountedSubtotal * 0.05;
    $delivery = 2.00;
    $totalAmount = (string)($discountedSubtotal + $tax + $delivery);

    // 4. Create Order
    try {
        $orderController = new OrderController(new Db());
        $orderId = $orderController->create($customerId, $totalAmount, 'pending', $address);

        // 5. Create Order Items
        $itemController = new OrderItemController(new Db());
        foreach ($orderItemsData as $item) {
            $itemController->create($orderId, $item['pid'], $item['qty'], $item['price']);
        }

        // 6. Mark Coupon as used
        if ($couponId) {
            $db = new Db();
            $db->run_query("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$couponId]);
        }

        // 7. Clear cart sessions
        unset($_SESSION['applied_coupon']);
        $cartController = new CartController(new Db());
        $cartController->clearCart($customerId);

        // 8. Return Success JSON
        echo json_encode([
            'status' => 'success',
            'order_id' => $orderId,
            'total' => number_format((float)$totalAmount, 2),
            'address' => $address,
            'date' => date('M d, Y - h:i A')
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>