<?php
require_once __DIR__ . '/../../Controllers/CategoryController.php';
require_once __DIR__ . '/../../Controllers/CouponController.php';
require_once __DIR__ . '/../../Config/DBconnection.php';

$activeCoupons = [];
try {
  $activeCoupons = (new CouponController(new Db()))->getPublicActive();
} catch (Throwable $error) {
  error_log('Home coupon load failed: ' . $error->getMessage());
}
$homeCategories = [];
try {
  $homeCategories = (new CategoryController())->getAll();
} catch (Throwable $error) {
  error_log('Home category load failed: ' . $error->getMessage());
}
$featuredProducts = [];
try {
  $featuredProducts = (new Db())->run_query(
    "SELECT p.id, p.name, p.price, p.stock_quantity, c.name AS category_name,
            (SELECT pi.image FROM product_images AS pi WHERE pi.product_id = p.id ORDER BY pi.id ASC LIMIT 1) AS image_path
     FROM products AS p
     INNER JOIN categories AS c ON c.id = p.category_id
     WHERE p.is_active = 1
     ORDER BY p.id DESC
     LIMIT 4"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $error) {
  error_log('Home featured products load failed: ' . $error->getMessage());
}
$productImageUrl = static function (?string $image): string {
  $image = trim(str_replace('\\', '/', (string) $image));
  if (preg_match('~\Ahttps?://~i', $image)) return $image;
  $image = preg_replace('~^/?Nexora/~i', '', $image);
  $image = ltrim($image, '/');
  if (!str_starts_with($image, 'Assets/Images/products/') || str_contains($image, '..')) return '';
  return '/Nexora/' . $image;
};
$categoryImageUrl = static function (?string $image): string {
  if (!$image) return '';
  if (preg_match('~\Ahttps?://~i', $image)) return $image;
  return '/Nexora/' . ltrim(str_replace('\\', '/', preg_replace('~\A/?Nexora/~i', '', $image)), '/');
};
$categoryIcon = static function (string $name): string {
  $name = strtolower($name);
  if (str_contains($name, 'laptop')) return '💻';
  if (str_contains($name, 'peripheral') || str_contains($name, 'accessor')) return '🎧';
  if (str_contains($name, 'component')) return '⚡';
  if (str_contains($name, 'pc') || str_contains($name, 'computer')) return '🖥️';
  return '◈';
};
$currentPage = 'home';
$pageTitle = 'NEXORA — Build Beyond Limits';
$pageStylesheet = 'landing.css';
$isAdminPage = false;
$showStoreFooter = true;
require_once __DIR__ . '/../Includes/header.php';

// CHANGED: limit to 7 coupons for the home ticker
$homeCoupons = array_slice($activeCoupons, 0, 7);
?>
    <!-- ================= NAVBAR ================= -->
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>

    <!-- ================= HERO ================= -->
    <section class="hero">
      <div class="hero-grid-bg"></div>
      <div class="container hero-inner">
        <div>
          <span class="hero-tag"><i></i> AI-powered tech store</span>
          <h1>
            BUILD <span class="text-grad glitch" data-text="BEYOND">BEYOND</span
            ><br />LIMITS.
          </h1>
          <p class="lead">
            Gaming PCs, components, laptops and peripherals — with an AI
            assistant, a smart PC Builder that checks compatibility, and
            side-by-side comparison so you always buy the right hardware.
          </p>
          <div class="hero-cta">
            <a href="../Layout/routing.php?page=products" class="btn btn-primary">Shop Now</a>
            <a href="../Layout/routing.php?page=products" class="btn btn-ghost">Build Your PC</a>
          </div>
          <div class="hero-stats">
            <div><strong>12K+</strong><span>Products</span></div>
            <div><strong>98%</strong><span>Compatibility match</span></div>
            <div><strong>24/7</strong><span>AI assistant</span></div>
          </div>
        </div>

        <!-- ===== Animated gaming PC (pure CSS) ===== -->
        <div class="rig" aria-hidden="true">
          <div class="rig-glow"></div>
          <div class="ring r1"></div>
          <div class="ring r2"></div>
          <div class="rig-floor"></div>

          <div class="orbiting-components" aria-hidden="true">
            <div class="orbit-item orbit-cpu"><img src="../../Assets/Images/home-components/cpu.png" alt="" loading="lazy" /></div>
            <div class="orbit-item orbit-ram"><img src="../../Assets/Images/home-components/ram.png" alt="" loading="lazy" /></div>
            <div class="orbit-item orbit-board"><img src="../../Assets/Images/home-components/motherboard.png" alt="" loading="lazy" /></div>
          </div>

          <div class="pc">
            <div class="pc-body"></div>
            <div class="pc-glass">
              <div class="side-fans">
                <span class="fan"></span><span class="fan"></span
                ><span class="fan"></span>
              </div>
              <div class="cpu"></div>
              <div class="hose"></div>
              <div class="ram"><i></i><i></i><i></i><i></i></div>
              <div class="gpu">
                <span class="fan"></span><span class="fan"></span>
              </div>
              <div class="psu"></div>
              <div class="scan"></div>
            </div>
            <div class="pc-front">
              <span class="led"></span><span class="strip"></span>
            </div>
          </div>

          <div class="orbit-ring" aria-hidden="true">
            <div class="floating-part part-cpu">
              <div class="floating-part-inner">
                <div class="part-art"><img src="../../Assets/Images/cpu.png" alt=""></div>
              </div>
            </div>
            <div class="floating-part part-ram">
              <div class="floating-part-inner">
                <div class="part-art"><img src="../../Assets/Images/RAM.png" alt=""></div>
              </div>
            </div>
            <div class="floating-part part-board">
              <div class="floating-part-inner">
                <div class="part-art"><img src="../../Assets/Images/motherboard.png" alt=""></div>
              </div>
            </div>
            <div class="floating-part part-ssd">
              <div class="floating-part-inner">
                <div class="part-art"><img src="../../Assets/Images/SSD.png" alt=""></div>
              </div>
            </div>
          </div>

          <div class="chip c1"><small>FPS</small><b>240+</b></div>
          <div class="chip c2"><small>GPU temp</small><b>58°C</b></div>
          <div class="chip c3"><small>Compatibility</small><b>100% ✓</b></div>

          <span class="spark"></span><span class="spark"></span
          ><span class="spark"></span> <span class="spark"></span
          ><span class="spark"></span><span class="spark"></span>
        </div>
      </div>
    </section>

    <script>
    (function () {
      function initOrbit() {
        var rig = document.querySelector('.rig');
        if (!rig) return;
        var ring = rig.querySelector('.orbit-ring');
        if (!ring) return;

        rig.style.setProperty('position', 'relative', 'important');
        ring.style.setProperty('position', 'absolute', 'important');
        ring.style.setProperty('top', '0', 'important');
        ring.style.setProperty('left', '0', 'important');
        ring.style.setProperty('width', '100%', 'important');
        ring.style.setProperty('height', '100%', 'important');
        ring.style.setProperty('margin', '0', 'important');
        ring.style.setProperty('pointer-events', 'none', 'important');

        var parts = [
          { el: ring.querySelector('.part-cpu'), angle: -90 },
          { el: ring.querySelector('.part-ram'), angle: 0 },
          { el: ring.querySelector('.part-board'), angle: 90 },
          { el: ring.querySelector('.part-ssd'), angle: 180 }
        ].filter(function (p) { return !!p.el; });

        parts.forEach(function (p) {
          p.el.style.setProperty('position', 'absolute', 'important');
          p.el.style.setProperty('top', '0', 'important');
          p.el.style.setProperty('left', '0', 'important');
          p.el.style.setProperty('margin', '0', 'important');
        });

        var DURATION = 20000;
        var HALF = 46;
        var start = performance.now();

        function radiusFor(w) {
          return Math.max(120, Math.min(w * 0.42, 260));
        }

        function tick(now) {
          var w = rig.clientWidth;
          var h = rig.clientHeight;
          if (w && h) {
            var r = radiusFor(w);
            var cx = w / 2;
            var cy = h / 2;
            var revolutions = (now - start) / DURATION;
            parts.forEach(function (p) {
              var deg = p.angle + revolutions * 360;
              var rad = (deg * Math.PI) / 180;
              var x = cx + r * Math.cos(rad) - HALF;
              var y = cy + r * Math.sin(rad) - HALF;
              p.el.style.setProperty(
                'transform',
                'translate(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px)',
                'important'
              );
            });
          }
          requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOrbit);
      } else {
        initOrbit();
      }
    })();
    </script>

    <!-- ================= BRANDS MARQUEE ================= -->
    <div class="marquee" aria-hidden="true">
      <div class="marquee-track">
        <span>NVIDIA</span><span>AMD</span><span>INTEL</span
        ><span>ASUS ROG</span><span>MSI</span><span>CORSAIR</span
        ><span>LOGITECH</span><span>RAZER</span> <span>NVIDIA</span
        ><span>AMD</span><span>INTEL</span><span>ASUS ROG</span><span>MSI</span
        ><span>CORSAIR</span><span>LOGITECH</span><span>RAZER</span>
      </div>
    </div>

    <!-- ================= CATEGORIES ================= -->
    <section class="section">
      <div class="container">
        <span class="eyebrow">// Categories</span>
        <h2 class="section-title">
          Pick your <span class="text-grad">arena</span>
        </h2>
        <p class="section-sub">
          Everything you need to build, upgrade and dominate.
        </p>

        <?php if ($homeCategories): ?>
          <div class="grid grid-4" style="margin-top: 40px">
            <?php foreach ($homeCategories as $category): ?>
              <?php $imageUrl = $categoryImageUrl($category['image'] ?? null); ?>
              <a href="../Layout/routing.php?page=products&amp;category=<?= (int) $category['id'] ?>" class="card cat-card">
                <div class="cat-icon">
                  <?php if ($imageUrl !== ''): ?>
                    <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                    <span class="cat-icon-fallback" hidden><?= $categoryIcon((string) $category['name']) ?></span>
                  <?php else: ?>
                    <?= $categoryIcon((string) $category['name']) ?>
                  <?php endif; ?>
                </div>
                <h3><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= (int) $category['product_count'] ?> <?= (int) $category['product_count'] === 1 ? 'product' : 'products' ?></p>
              </a>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="section-sub" style="margin-top:40px">No categories are available yet.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- ================= FEATURED PRODUCTS ================= -->
    <section class="section" style="background: var(--bg-2)">
      <div class="container">
        <span class="eyebrow">// Featured</span>
        <h2 class="section-title">
          Trending <span class="text-grad">gear</span>
        </h2>

        <?php if ($featuredProducts): ?>
          <div class="grid grid-4" style="margin-top: 40px">
            <?php foreach ($featuredProducts as $product): ?>
              <?php
                $detailsUrl = '../Layout/routing.php?page=product&id=' . (int) $product['id'];
                $imageUrl = $productImageUrl($product['image_path'] ?? null);
              ?>
              <article class="card p-card">
                <a href="<?= htmlspecialchars($detailsUrl, ENT_QUOTES, 'UTF-8') ?>" class="p-img">
                  <?php if ($imageUrl !== ''): ?>
                    <img class="catalog-product-image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                  <?php else: ?>◈<?php endif; ?>
                </a>
                <div class="p-body">
                  <span class="p-cat"><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                  <a href="<?= htmlspecialchars($detailsUrl, ENT_QUOTES, 'UTF-8') ?>" class="p-name"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></a>
                  <div class="p-foot">
                    <span class="p-price"><?= number_format((float) $product['price'], 2) ?> <small>JD</small></span>
                    <a href="<?= htmlspecialchars($detailsUrl, ENT_QUOTES, 'UTF-8') ?>#purchase" class="btn btn-primary btn-sm">View &amp; buy</a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="section-sub" style="margin-top:40px">No products are available yet. <a href="../Layout/routing.php?page=products">Browse the catalog</a></p>
        <?php endif; ?>
      </div>
    </section>

    <!-- ================= PROMO / COUPONS (ROTATING TICKER, UP TO 7) ================= -->
    <?php if (!empty($homeCoupons)): ?>
    <style>
      /* CHANGED: horizontal rotating coupon ticker */
      .coupon-ticker-wrap {
        position: relative;
        overflow: hidden;
        margin-top: 30px;
        -webkit-mask-image: linear-gradient(to right, transparent 0, #000 6%, #000 94%, transparent 100%);
                mask-image: linear-gradient(to right, transparent 0, #000 6%, #000 94%, transparent 100%);
      }
      .coupon-ticker {
        display: flex;
        gap: 20px;
        width: max-content;
        animation: couponScroll 40s linear infinite;
        will-change: transform;
      }
      .coupon-ticker-wrap:hover .coupon-ticker {
        animation-play-state: paused;
      }
      @keyframes couponScroll {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
      }
      .coupon-tile {
        width: 300px;
        flex: 0 0 300px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        border-color: rgba(34, 211, 238, 0.4);
      }
      @media (max-width: 600px) {
        .coupon-tile { width: 240px; flex: 0 0 240px; padding: 18px; }
      }
      @media (prefers-reduced-motion: reduce) {
        .coupon-ticker { animation: none; }
      }
    </style>

    <section class="section" style="padding-top: 40px;">
      <div class="container">
        <span class="eyebrow">// Player Perks</span>
        <h2 class="section-title">Unlock exclusive <span class="text-grad">deals</span></h2>
        <p class="section-sub">Grab these codes before they expire and boost your setup.</p>

        <div class="coupon-ticker-wrap">
          <div class="coupon-ticker">
            <?php
              // CHANGED: render the list twice for a seamless infinite loop
              for ($loop = 0; $loop < 2; $loop++):
                foreach ($homeCoupons as $coupon):
            ?>
              <div class="card coupon-tile">
                <!-- Decorative glow -->
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: var(--cyan); filter: blur(40px); opacity: 0.3; pointer-events: none;"></div>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                  <span class="badge badge-ok">Active</span>
                  <span style="font-family: var(--font-head); font-size: 1.2rem; color: var(--cyan);">
                    <?= $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : $coupon->discount_value . ' JD' ?> OFF
                  </span>
                </div>

                <h3 style="font-family: var(--font-head); font-size: 1.4rem; letter-spacing: 2px; margin: 10px 0; color: #fff;">
                  <?php if (isset($_SESSION['customer_id'])): ?>
                      <?= htmlspecialchars($coupon->code) ?>
                  <?php else: ?>
                      ••••••••
                  <?php endif; ?>
                </h3>

                <p class="muted" style="font-size: 0.8rem; margin-bottom: 16px;">
                  Min. order: <b><?= number_format($coupon->min_order_amount, 2) ?> JD</b>
                </p>

                <div style="display: flex; gap: 8px; align-items: center;">
                  <?php if (isset($_SESSION['customer_id'])): ?>
                      <button class="btn btn-primary btn-sm full copy-btn" data-code="<?= htmlspecialchars($coupon->code) ?>">
                        Copy Code
                      </button>
                  <?php else: ?>
                      <a href="../Layout/routing.php?page=login" class="btn btn-ghost btn-sm full" style="border-color: var(--purple);">
                        Log in to unlock
                      </a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; endfor; ?>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= SMART FEATURES ================= -->
    <section class="section">
      <div class="container">
        <span class="eyebrow">// Why NEXORA</span>
        <h2 class="section-title">
          Smarter than a <span class="text-grad">store</span>
        </h2>

        <div class="grid grid-3" style="margin-top: 40px">
          <div class="card feat">
            <div class="feat-num">01</div>
            <h3>AI Shopping Assistant</h3>
            <p>
              Tell it your budget and games — get tailored recommendations and
              clear technical explanations.
            </p>
          </div>
          <div class="card feat">
            <div class="feat-num">02</div>
            <h3>Smart PC Builder</h3>
            <p>
              Pick parts with live compatibility checks, price totals and
              alternative suggestions.
            </p>
          </div>
          <div class="card feat">
            <div class="feat-num">03</div>
            <h3>Product Comparison</h3>
            <p>
              Compare specs, price and performance side by side and choose with
              confidence.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= CTA ================= -->
    <section class="section" style="padding-top: 0">
      <div class="container">
        <div class="cta-band">
          <h2>Ready to <span class="text-grad">level up</span>?</h2>
          <p>Create your account and start building your dream rig today.</p>
          <a href="../Layout/routing.php?page=register" class="btn btn-primary">Create account</a>
        </div>
      </div>
    </section>

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
    <!-- ================= FOOTER ================= -->
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
