<?php
require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';

$currentPage = 'my-coupons';
$pageTitle = 'My Coupons — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

$userId = $_SESSION['customer_id'] ?? 1;
$couponController = new CouponController(new Db());

$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

$totalCoupons = $couponController->countUserCoupons($userId);
$totalPages = ceil($totalCoupons / $limit);

$myCoupons = [];
if ($totalCoupons > 0) {
    $myCoupons = $couponController->getUserCoupons($userId, $limit, $offset);
}

require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/user_nav.php'; ?>

<main class="container page-main">
    <span class="eyebrow">// LOOT VAULT</span>
    <h1 class="page-title">Your <span class="text-grad">coupons.</span></h1>

    <div class="account-layout">
        <aside class="account-nav card">
          <div class="avatar"><?= strtoupper(substr($_SESSION['customer_name'] ?? 'C', 0, 1)) ?></div>
          <h3><?= htmlspecialchars($_SESSION['customer_name'] ?? 'Customer') ?></h3>
          <small>MEMBER SINCE 2026</small>
          <a href="../Layout/routing.php?page=account">Profile overview</a>
          <a href="../Layout/routing.php?page=my_orders">My orders</a>
          <a href="../Layout/routing.php?page=wishlist">Wishlist</a>
          <a class="active" href="../Layout/routing.php?page=my-coupons">My Coupons</a>
          <a href="../Layout/routing.php?page=account#settings">Account settings</a>
        </aside>

        <section>
            <p class="muted" style="margin-bottom: 30px;">Claim your rewards and apply them at checkout to save on your next upgrade.</p>

            <?php if (empty($myCoupons)): ?>
                <div class="card profile-card" style="text-align: center; padding: 50px;">
                    <h2>No coupons available yet.</h2>
                    <p class="muted">Keep shopping and leveling up to unlock exclusive deals!</p>
                    <a href="../Layout/routing.php?page=products" class="btn btn-primary" style="margin-top: 20px;">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="grid grid-3">
                    <?php foreach ($myCoupons as $coupon): ?>
                        <div class="card" style="padding: 24px 20px; border: 1px solid var(--line); position: relative; display: flex; flex-direction: column; justify-content: space-between;">
                            
                            <div style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 10px; height: 20px; background: var(--bg); border-radius: 0 10px 10px 0; border-right: 1px solid var(--line);"></div>
                            <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 10px; height: 20px; background: var(--bg); border-radius: 10px 0 0 10px; border-left: 1px solid var(--line);"></div>

                            <!-- Top Row: Badge and Expiry -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 15px; flex-wrap: wrap;">
                                <span class="badge <?= $coupon->discount_type === 'percentage' ? 'badge-hot' : 'badge-ok' ?>">
                                    <?= $coupon->discount_type === 'percentage' ? $coupon->discount_value . '% OFF' : $coupon->discount_value . ' JD OFF' ?>
                                </span>
                                <small style="color: var(--muted); font-size: 0.75rem; line-height: 1.2; text-align: right;">
                                    Expires:<br><?= date('M d, Y', strtotime($coupon->valid_until)) ?>
                                </small>
                            </div>

                            <!-- Coupon Code (Fixed for long text) -->
                            <h3 style="font-family: var(--font-head); font-size: 1.2rem; text-align: center; margin: 5px 0 15px; color: #fff; background: rgba(139,92,246,0.1); padding: 12px 10px; border-radius: 8px; border: 1px dashed var(--line); word-break: break-all; overflow-wrap: anywhere; line-height: 1.3;">
                                <?= htmlspecialchars($coupon->code) ?>
                            </h3>

                            <!-- Bottom Row -->
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--muted); margin-bottom: 15px; gap: 8px;">
                                <span style="white-space: nowrap;">Min: <b><?= number_format($coupon->min_order_amount, 0) ?> JD</b></span>
                                <span style="white-space: nowrap;">Uses: <b><?= $coupon->usage_limit - $coupon->used_count ?></b></span>
                            </div>

                            <button class="btn btn-ghost btn-sm full copy-btn" style="margin-top: auto;" data-code="<?= htmlspecialchars($coupon->code) ?>">Copy Code</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div style="margin-top: 40px; display: flex; justify-content: center; gap: 10px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="../Layout/routing.php?page=my-coupons&p=<?= $i ?>"
                           class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-ghost' ?>"
                           style="min-width: 35px; padding: 5px 10px;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            <?php endif; ?>
        </section>
    </div>
</main>

<script>
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const code = this.getAttribute('data-code');
        navigator.clipboard.writeText(code).then(() => {
            const originalText = this.innerHTML;
            this.innerHTML = '✓ Copied!';
            this.style.background = 'var(--green)';
            this.style.color = '#000';
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.background = '';
                this.style.color = '';
            }, 2000);
        });
    });
});
</script>

<?php require_once __DIR__ . '/../Includes/footer.php'; ?>