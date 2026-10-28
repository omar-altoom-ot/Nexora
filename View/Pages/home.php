<?php
$currentPage = 'home';
$pageTitle = 'NEXORA — Build Beyond Limits';
$pageStylesheet = 'landing.css';
$isAdminPage = false;
$showStoreFooter = true;
require_once __DIR__ . '/../Includes/header.php';
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
            <a href="/pc-builder" class="btn btn-ghost">Build Your PC</a>
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

          <div class="chip c1"><small>FPS</small><b>240+</b></div>
          <div class="chip c2"><small>GPU temp</small><b>58°C</b></div>
          <div class="chip c3"><small>Compatibility</small><b>100% ✓</b></div>

          <span class="spark"></span><span class="spark"></span
          ><span class="spark"></span> <span class="spark"></span
          ><span class="spark"></span><span class="spark"></span>
        </div>
      </div>
    </section>

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

        <div class="grid grid-4" style="margin-top: 40px">
          <a href="../Layout/routing.php?page=products&amp;category=1" class="card cat-card"
            ><div class="cat-icon">🖥️</div>
            <h3>Gaming PCs</h3>
            <p>Prebuilt monsters</p></a
          >
          <a href="../Layout/routing.php?page=products&amp;category=2" class="card cat-card"
            ><div class="cat-icon">⚡</div>
            <h3>Components</h3>
            <p>CPU · GPU · RAM · SSD</p></a
          >
          <a href="../Layout/routing.php?page=products&amp;category=3" class="card cat-card"
            ><div class="cat-icon">💻</div>
            <h3>Laptops</h3>
            <p>Gaming &amp; creator</p></a
          >
          <a href="../Layout/routing.php?page=products&amp;category=4" class="card cat-card"
            ><div class="cat-icon">🎧</div>
            <h3>Peripherals</h3>
            <p>Mice · keyboards · headsets</p></a
          >
        </div>
      </div>
    </section>

    <!-- ================= FEATURED PRODUCTS ================= -->
    <section class="section" style="background: var(--bg-2)">
      <div class="container">
        <span class="eyebrow">// Featured</span>
        <h2 class="section-title">
          Trending <span class="text-grad">gear</span>
        </h2>

        <div class="grid grid-4" style="margin-top: 40px">
          <!-- Add to cart as guest → /login -->
          <article class="card p-card">
            <a href="../Layout/routing.php?page=product" class="p-img"
              ><span class="badge badge-hot">Hot</span>🎮</a
            >
            <div class="p-body">
              <span class="p-cat">Graphics Cards</span>
              <a href="../Layout/routing.php?page=product" class="p-name">RTX 5080 16GB Gaming OC</a>
              <span class="p-stars">★★★★★</span>
              <div class="p-foot">
                <span class="p-price">1,199 <small>JD</small></span>
                <a href="../Layout/routing.php?page=login" class="btn btn-primary btn-sm">Add to cart</a>
              </div>
            </div>
          </article>

          <article class="card p-card">
            <a href="../Layout/routing.php?page=product" class="p-img"
              ><span class="badge">New</span>🧠</a
            >
            <div class="p-body">
              <span class="p-cat">Processors</span>
              <a href="../Layout/routing.php?page=product" class="p-name">Ryzen 9 9950X 16-Core</a>
              <span class="p-stars">★★★★☆</span>
              <div class="p-foot">
                <span class="p-price">549 <small>JD</small></span>
                <a href="../Layout/routing.php?page=login" class="btn btn-primary btn-sm">Add to cart</a>
              </div>
            </div>
          </article>

          <article class="card p-card">
            <a href="../Layout/routing.php?page=product" class="p-img"
              ><span class="badge badge-ok">In stock</span>⌨️</a
            >
            <div class="p-body">
              <span class="p-cat">Peripherals</span>
              <a href="../Layout/routing.php?page=product" class="p-name"
                >Mechanical RGB Keyboard 75%</a
              >
              <span class="p-stars">★★★★★</span>
              <div class="p-foot">
                <span class="p-price">89 <small>JD</small></span>
                <a href="../Layout/routing.php?page=login" class="btn btn-primary btn-sm">Add to cart</a>
              </div>
            </div>
          </article>

          <article class="card p-card">
            <a href="../Layout/routing.php?page=product" class="p-img"
              ><span class="badge badge-hot">-15%</span>💾</a
            >
            <div class="p-body">
              <span class="p-cat">Storage</span>
              <a href="../Layout/routing.php?page=product" class="p-name">NVMe Gen5 SSD 2TB</a>
              <span class="p-stars">★★★★☆</span>
              <div class="p-foot">
                <span class="p-price">169 <small>JD</small></span>
                <a href="../Layout/routing.php?page=login" class="btn btn-primary btn-sm">Add to cart</a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

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

    <!-- ================= FOOTER ================= -->
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

