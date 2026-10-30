
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CartController.php';

if (
    !isset($_SESSION['customer_id']) ||
    ($_SESSION['user_role'] ?? null) !== 'customer'
) {
    header('Location: ../Layout/routing.php?page=login');
    exit;
}

$db = new Db();
$cartController = new CartController($db);
$customerId = (int) $_SESSION['customer_id'];

if (empty($_SESSION['cart_csrf_token'])) {
    $_SESSION['cart_csrf_token'] = bin2hex(random_bytes(32));
}

$cartUrl = '../Layout/routing.php?page=cart';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['cart_csrf_token'], $token)
    ) {
        http_response_code(403);
        $error = 'Invalid request. Please refresh the page.';
    } else {
        $action = $_POST['action'] ?? '';
        $productId = filter_var(
            $_POST['product_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($productId === false || $productId === null || $productId <= 0) {
            $error = 'Invalid product.';
        } else {
            $success = false;

            if ($action === 'remove') {
                $success = $cartController->removeItem(
                    $customerId,
                    $productId
                );
            } elseif ($action === 'update') {
                $quantity = filter_var(
                    $_POST['quantity'] ?? null,
                    FILTER_VALIDATE_INT
                );

                if ($quantity !== false && $quantity !== null && $quantity > 0) {
                    $success = $cartController->updateQuantity(
                        $customerId,
                        $productId,
                        $quantity
                    );
                }
            }

            if ($success) {
                header('Location: ' . $cartUrl);
                exit;
            }

            $error = 'Could not update your cart. Check the available stock.';
        }
    }
}

$items = $cartController->getItems($customerId);

$subtotal = 0;

foreach ($items as $item) {
    $subtotal += (float) $item['price'] * (int) $item['quantity'];
}

$tax = round($subtotal * 0.05, 2);
$delivery = empty($items) ? 0 : 2.00;
$total = $subtotal + $tax + $delivery;

$currentPage = 'cart';
$pageTitle = 'Your cart — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

require_once __DIR__ . '/../Includes/header.php';
?>

<?php require __DIR__ . '/../Layout/user_nav.php'; ?>

<main class="container page-main">

    <span class="eyebrow">// YOUR LOADOUT</span>

    <h1 class="page-title">
        Shopping <span class="text-grad">cart.</span>
    </h1>

    <?php if ($error !== ''): ?>
        <p role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="cart-layout">

        <section class="cart-items">

            <?php if (empty($items)): ?>

                <article class="cart-item card">
                    <div class="item-copy">
                        <h3>Your cart is empty.</h3>
                        <span>Add products to start shopping.</span>
                    </div>
                </article>

            <?php else: ?>

                <?php foreach ($items as $item): ?>

                    <?php
                    $productId = (int) $item['id'];
                    $quantity = (int) $item['quantity'];
                    $price = (float) $item['price'];
                    $lineTotal = $price * $quantity;
                    ?>

                    <article class="cart-item card">

                        <div class="mini-visual">📦</div>

                        <div class="item-copy">

                            <small>PRODUCT</small>

                            <h3>
                                <?= htmlspecialchars(
                                    $item['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </h3>

                            <span>
                                SKU NX-<?= $productId ?>
                            </span>

                            <form method="POST" action="<?= $cartUrl ?>">
                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?= htmlspecialchars($_SESSION['cart_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                                <input type="hidden"
                                       name="action"
                                       value="remove">

                                <input type="hidden"
                                       name="product_id"
                                       value="<?= $productId ?>">

                                <a href="#" onclick="event.preventDefault(); this.closest('form').requestSubmit();">Remove</a>
                            </form>

                        </div>

                        <div class="quantity">

                            <form method="POST" action="<?= $cartUrl ?>">
                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?= htmlspecialchars($_SESSION['cart_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                                <input type="hidden"
                                       name="action"
                                       value="update">

                                <input type="hidden"
                                       name="product_id"
                                       value="<?= $productId ?>">

                                <input type="hidden"
                                       name="quantity"
                                       value="<?= $quantity - 1 ?>">

                                <button type="submit"
                                        <?= $quantity <= 1 ? 'disabled' : '' ?>>
                                    −
                                </button>
                            </form>

                            <b><?= $quantity ?></b>

                            <form method="POST" action="<?= $cartUrl ?>">
                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?= htmlspecialchars($_SESSION['cart_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                                <input type="hidden"
                                       name="action"
                                       value="update">

                                <input type="hidden"
                                       name="product_id"
                                       value="<?= $productId ?>">

                                <input type="hidden"
                                       name="quantity"
                                       value="<?= $quantity + 1 ?>">

                                <button type="submit">+</button>
                            </form>

                        </div>

                        <b class="item-price">
                            <?= number_format($lineTotal, 2) ?> JD
                        </b>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

            <a href="../Layout/routing.php?page=products"
               class="back-link">
                ← Continue shopping
            </a>

        </section>

        <aside class="summary card">

            <h2>Order summary</h2>

            <div class="coupon">
                <input class="input"
                       placeholder="Coupon code"
                       disabled>

                <button class="btn btn-ghost" disabled>
                    Apply
                </button>
            </div>

            <div class="summary-line">
                <span>Subtotal</span>
                <b><?= number_format($subtotal, 2) ?> JD</b>
            </div>

            <div class="summary-line">
                <span>Estimated tax (5%)</span>
                <b><?= number_format($tax, 2) ?> JD</b>
            </div>

            <div class="summary-line">
                <span>Delivery</span>
                <b><?= number_format($delivery, 2) ?> JD</b>
            </div>

            <div class="summary-total">
                <span>Total</span>
                <b><?= number_format($total, 2) ?> JD</b>
            </div>

            <?php if (!empty($items)): ?>

                <a class="btn btn-primary full"
                   href="../Layout/routing.php?page=checkout">
                    Continue to checkout
                </a>

            <?php else: ?>

                <a class="btn btn-primary full"
                   href="../Layout/routing.php?page=products">
                    Browse products
                </a>

            <?php endif; ?>

            <a class="cancel-link"
               href="../Layout/routing.php?page=products">
                Continue shopping
            </a>

            <small class="secure">
                ◈ Secure checkout · Tax calculated at 5%
            </small>

        </aside>

    </div>

</main>

<nav class="page-links" aria-label="Store pages">
    <a href="../Layout/routing.php?page=home">Home</a>
    <a href="../Layout/routing.php?page=products">Products</a>
    <a href="../Layout/routing.php?page=cart">Cart</a>
    <a href="../Layout/routing.php?page=checkout">Checkout</a>
    <a href="../Layout/routing.php?page=account">My account</a>
    <a href="../Layout/routing.php?page=wishlist">Wishlist</a>
    <a href="../Layout/routing.php?page=login">Login</a>
    <a href="../Layout/routing.php?page=register">Register</a>
</nav>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
