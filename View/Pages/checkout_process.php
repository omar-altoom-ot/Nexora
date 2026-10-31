<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function checkoutResponse(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') checkoutResponse(405, ['status' => 'error', 'message' => 'Use the checkout form to place an order.']);
$customerId = (int) ($_SESSION['customer_id'] ?? 0);
$token = $_POST['csrf_token'] ?? '';
if ($customerId <= 0 || ($_SESSION['user_role'] ?? '') !== 'customer') checkoutResponse(401, ['status' => 'error', 'message' => 'Please sign in again.']);
if (!is_string($token) || empty($_SESSION['checkout_csrf_token']) || !hash_equals($_SESSION['checkout_csrf_token'], $token)) {
    checkoutResponse(403, ['status' => 'error', 'message' => 'Your checkout session expired. Reload the page and try again.']);
}
$address = trim((string) ($_POST['shipping_address'] ?? ''));
if ($address === '' || mb_strlen($address) > 255) checkoutResponse(422, ['status' => 'error', 'message' => 'Choose a valid delivery location.']);

$cart = $_SESSION['cart_items'] ?? [];
if (!is_array($cart) || !$cart) checkoutResponse(409, ['status' => 'error', 'message' => 'Your cart is empty. Add products before checkout.']);
ksort($cart, SORT_NUMERIC);

require_once __DIR__ . '/../../Config/DBconnection.php';
$db = new Db();
$db->beginTransaction();
try {
    // Serialize this order against simultaneous checkout requests for the customer.
    $customer = $db->run_query('SELECT id FROM customers WHERE id = ? FOR UPDATE', [$customerId])->fetch();
    if (!$customer) throw new RuntimeException('Customer account was not found.');

    $items = [];
    $subtotal = 0.0;
    foreach ($cart as $productId => $cartItem) {
        $productId = filter_var($productId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $quantity = is_array($cartItem) ? (int) ($cartItem['quantity'] ?? 0) : 0;
        $reserved = is_array($cartItem) ? (int) ($cartItem['stock_reserved_quantity'] ?? 0) : 0;
        if (!$productId || $quantity <= 0 || $reserved < $quantity) {
            throw new RuntimeException('Cart data is invalid or stock is no longer reserved.');
        }
        $product = $db->run_query('SELECT id, name, price FROM products WHERE id = ? FOR UPDATE', [$productId])->fetch();
        if (!$product) throw new RuntimeException('A product in the cart is no longer available.');
        $product['quantity'] = $quantity;
        $items[] = $product;
        $subtotal += (float) $product['price'] * $quantity;
    }

    $total = round($subtotal + ($subtotal * 0.05) + 2.00, 2);
    $db->run_query(
        'INSERT INTO orders (customer_id, total_amount, status, shipping_address) VALUES (?, ?, ?, ?)',
        [$customerId, number_format($total, 2, '.', ''), 'pending', $address]
    );
    $orderId = (int) $db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
    foreach ($items as $item) {
        $db->run_query(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)',
            [$orderId, (int) $item['id'], (int) $item['quantity'], (float) $item['price']]
        );
    }

    $db->commit();
    // The stock was already reduced when items entered the session cart.
    $_SESSION['cart_items'] = [];
    unset($_SESSION['cart']);
    $_SESSION['checkout_csrf_token'] = bin2hex(random_bytes(32));

    checkoutResponse(200, [
        'status' => 'success',
        'order_id' => $orderId,
        'total' => number_format($total, 2),
        'address' => $address,
        'date' => date('M d, Y - h:i A'),
    ]);
} catch (Throwable $error) {
    $db->rollBack();
    error_log('Checkout failed: ' . $error->getMessage());
    checkoutResponse(500, ['status' => 'error', 'message' => 'We could not place your order. Please try again.']);
}
