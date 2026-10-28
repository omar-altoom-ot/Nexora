<?php
$currentPage = 'products';
$pageTitle = 'Products — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = true;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/user_nav.php'; ?>
    <main class="container page-main">
      <div class="page-heading">
        <div>
          <span class="eyebrow">// THE GEAR VAULT</span>
          <h1>Find your next <span class="text-grad">upgrade.</span></h1>
          <p class="muted">Curated hardware. Clear specs. Zero guesswork.</p>
        </div>
        <label class="search"
          ><span>⌕</span><input placeholder="Search products..."
        /></label>
      </div>
      <div class="shop-layout">
        <aside class="filter-panel card">
          <h3>Categories</h3>
          <a class="filter-active" href="../Layout/routing.php?page=products"
            >All products <b>128</b></a
          ><a href="../Layout/routing.php?page=products&amp;category=components">Components <b>42</b></a
          ><a href="../Layout/routing.php?page=products&amp;category=pcs">Gaming PCs <b>16</b></a
          ><a href="../Layout/routing.php?page=products&amp;category=laptops">Laptops <b>22</b></a
          ><a href="../Layout/routing.php?page=products&amp;category=peripherals"
            >Peripherals <b>31</b></a
          >
          <hr />
          <h3>Price range</h3>
          <div class="price-range">
            <input class="input" placeholder="Min JD" /><input
              class="input"
              placeholder="Max JD"
            />
          </div>
          <button class="btn btn-ghost full">Apply filters</button>
        </aside>
        <section>
          <div class="catalog-toolbar">
            <span>Showing <b>1–4</b> of 128 products</span
            ><select class="input">
              <option>Featured</option>
              <option>Price: low to high</option>
              <option>Top rated</option>
            </select>
          </div>
          <div class="grid grid-4 product-grid">
            <article class="card p-card">
              <a class="p-img" href="../Layout/routing.php?page=product"
                ><span class="badge badge-hot">HOT</span>🎮</a
              >
              <div class="p-body">
                <span class="p-cat">GRAPHICS CARDS</span
                ><a class="p-name" href="../Layout/routing.php?page=product"
                  >RTX 5080 16GB Gaming OC</a
                ><span class="p-stars">★★★★★ <small>4.9 (38)</small></span>
                <div class="p-foot">
                  <span class="p-price">1,199 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
            <article class="card p-card">
              <a class="p-img" href="../Layout/routing.php?page=product"
                ><span class="badge">NEW</span>🧠</a
              >
              <div class="p-body">
                <span class="p-cat">PROCESSORS</span
                ><a class="p-name" href="../Layout/routing.php?page=product">Ryzen 9 9950X 16-Core</a
                ><span class="p-stars">★★★★★ <small>4.8 (26)</small></span>
                <div class="p-foot">
                  <span class="p-price">549 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
            <article class="card p-card">
              <a class="p-img" href="../Layout/routing.php?page=product"
                ><span class="badge badge-ok">IN STOCK</span>⌨️</a
              >
              <div class="p-body">
                <span class="p-cat">PERIPHERALS</span
                ><a class="p-name" href="../Layout/routing.php?page=product"
                  >Mechanical RGB Keyboard 75%</a
                ><span class="p-stars">★★★★★ <small>5.0 (14)</small></span>
                <div class="p-foot">
                  <span class="p-price">89 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
            <article class="card p-card">
              <a class="p-img" href="../Layout/routing.php?page=product"
                ><span class="badge badge-hot">−15%</span>💾</a
              >
              <div class="p-body">
                <span class="p-cat">STORAGE</span
                ><a class="p-name" href="../Layout/routing.php?page=product">NVMe Gen5 SSD 2TB</a
                ><span class="p-stars">★★★★☆ <small>4.6 (19)</small></span>
                <div class="p-foot">
                  <span class="p-price">169 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
          </div>
          <div class="pagination">
            <button>←</button><button class="selected">1</button
            ><button>2</button><button>3</button><button>→</button>
          </div>
        </section>
      </div>
    </main>
    <nav class="page-links" aria-label="Store pages">
      <a href="../Layout/routing.php?page=home">Home</a><a href="../Layout/routing.php?page=products">Products</a
      ><a href="../Layout/routing.php?page=cart">Cart</a><a href="../Layout/routing.php?page=checkout">Checkout</a
      ><a href="../Layout/routing.php?page=account">My account</a><a href="../Layout/routing.php?page=wishlist">Wishlist</a
      ><a href="../Layout/routing.php?page=login">Login</a><a href="../Layout/routing.php?page=register">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

