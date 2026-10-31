<?php
require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';

$currentPage = 'my-coupons';
$pageTitle = 'My Coupons — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = false;

// FIX: Use customer_id (matches routing.php)
$userId = $_SESSION['customer_id'] ?? 1;

$couponController = new CouponController(new Db());
$myCoupons = $couponController->getUserCoupons($userId);

require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/user_nav.php'; ?>

<main class="container page-main">
    <span class="eyebrow">// LOOT VAULT</span>
    <h1 class="page-title">Your <span class="text-grad">coupons.</span></h1>
    
    <div class="account-layout">
        <!-- ADDED SIDEBAR HERE -->
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
                        <div class="card" style="padding: 24px; border: 1px solid var(--line); position: relative;">
                            <!-- Perforated edge effect -->
                            <div style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 10px; height: 20px; background: var(--bg); border-radius: 0 10px 10px 0; border-right: 1px solid var(--line);"></div>
                            <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 10px; height: 20px; background: var(--bg); border-radius: 10px 0 0 10px; border-left: 1px solid var(--line);"></div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span class="badge <?= $coupon->discount_type === 'percentage' ? 'badge-hot' : 'badge-ok' ?>">
                                    <?= $coupon->discount_type === 'percentage' ? $coupon->discount_value . '% OFF' : $coupon->discount_value . ' JD OFF' ?>
                                </span>
                                <small style="color: var(--muted);">Expires: <?= date('M d, Y', strtotime($coupon->valid_until)) ?></small>
                            </div>

                            <h3 style="font-family: var(--font-head); font-size: 1.6rem; letter-spacing: 2px; text-align: center; margin: 15px 0; color: #fff; background: rgba(139,92,246,0.1); padding: 10px; border-radius: 8px; border: 1px dashed var(--line);">
                                <?= htmlspecialchars($coupon->code) ?>
                            </h3>

                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--muted); margin-bottom: 15px;">
                                <span>Min. Spend: <b><?= number_format($coupon->min_order_amount, 2) ?> JD</b></span>
                                <span>Uses left: <b><?= $coupon->usage_limit - $coupon->used_count ?></b></span>
                            </div>

                            <button class="btn btn-ghost btn-sm full copy-btn" data-code="<?= htmlspecialchars($coupon->code) ?>">Copy Code</button>
                        </div>
                    <?php endforeach; ?>
                </div>
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