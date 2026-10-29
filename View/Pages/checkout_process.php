<?php
session_start();

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/orders.php';
require_once __DIR__ . '/../../Controllers/OrderItemController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get Customer ID (Fallback to 1 for testing)
    $customerId = $_SESSION['customer_id'] ?? 1; 
    
    // 2. Get the address from the hidden input (Populated by Leaflet)
    $address = !empty($_POST['shipping_address']) ? $_POST['shipping_address'] : 'No address provided';
    
    // 3. Calculate Total (Hardcoded for now, ideally from Cart)
    // $totalAmount = '1354.40';
	// 3. Calculate Total from the hidden inputs sent by checkout.php
$totalAmount = 0;
if (isset($_POST['cart_items']) && is_array($_POST['cart_items'])) {
    foreach ($_POST['cart_items'] as $itemString) {
        list($pid, $qty, $price) = explode('|', $itemString);
        $totalAmount += ($qty * $price);
    }
}
$totalAmount = (string)($totalAmount + ($totalAmount * 0.05) + 2.00); // Add tax and delivery

    // 4. Instantiate the Controller
    $orderController = new OrderController(new Db());
    
    // 5. Create the Order
    $orderId = $orderController->create($customerId, $totalAmount, 'pending', $address);

    // 6. Create the Order Items
	    // 6. Create the Order Items
    $itemController = new OrderItemController(new Db());

    if (isset($_POST['cart_items']) && is_array($_POST['cart_items'])) {
        foreach ($_POST['cart_items'] as $itemString) {
            list($pid, $qty, $price) = explode('|', $itemString);
            $itemController->create($orderId, (int)$pid, (int)$qty, (float)$price);
        }
    }
    // $itemController = new OrderItemController(new Db());
    
    // // Loop through Cart Session (You need to set this up!)
    // if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    //     foreach ($_SESSION['cart'] as $productId => $item) {
    //         $itemController->create($orderId, $productId, $item['quantity'], $item['price']);
    //     }
    // } else {
    //     // Fallback for testing so the order isn't empty
    //     $itemController->create($orderId, 1, 1, 599.99);
    //     $itemController->create($orderId, 6, 1, 104.99);
    // }

// 7. Clear the cart
    unset($_SESSION['cart']);

    // 8. Return JSON Response for the Modal (NO REDIRECT!)
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