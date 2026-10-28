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
          <a class="filter-active" href="products.php"
            >All products <b>128</b></a
          ><a href="products.php?category=components">Components <b>42</b></a
          ><a href="products.php?category=pcs">Gaming PCs <b>16</b></a
          ><a href="products.php?category=laptops">Laptops <b>22</b></a
          ><a href="products.php?category=peripherals"
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
              <a class="p-img" href="product.php"
                ><span class="badge badge-hot">HOT</span>🎮</a
              >
              <div class="p-body">
                <span class="p-cat">GRAPHICS CARDS</span
                ><a class="p-name" href="product.php"
                  >RTX 5080 16GB Gaming OC</a
                ><span class="p-stars">★★★★★ <small>4.9 (38)</small></span>
                <div class="p-foot">
                  <span class="p-price">1,199 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="login.php"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
            <article class="card p-card">
              <a class="p-img" href="product.php"
                ><span class="badge">NEW</span>🧠</a
              >
              <div class="p-body">
                <span class="p-cat">PROCESSORS</span
                ><a class="p-name" href="product.php">Ryzen 9 9950X 16-Core</a
                ><span class="p-stars">★★★★★ <small>4.8 (26)</small></span>
                <div class="p-foot">
                  <span class="p-price">549 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="login.php"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
            <article class="card p-card">
              <a class="p-img" href="product.php"
                ><span class="badge badge-ok">IN STOCK</span>⌨️</a
              >
              <div class="p-body">
                <span class="p-cat">PERIPHERALS</span
                ><a class="p-name" href="product.php"
                  >Mechanical RGB Keyboard 75%</a
                ><span class="p-stars">★★★★★ <small>5.0 (14)</small></span>
                <div class="p-foot">
                  <span class="p-price">89 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="login.php"
                    >Add to cart</a
                  >
                </div>
              </div>
            </article>
            <article class="card p-card">
              <a class="p-img" href="product.php"
                ><span class="badge badge-hot">−15%</span>💾</a
              >
              <div class="p-body">
                <span class="p-cat">STORAGE</span
                ><a class="p-name" href="product.php">NVMe Gen5 SSD 2TB</a
                ><span class="p-stars">★★★★☆ <small>4.6 (19)</small></span>
                <div class="p-foot">
                  <span class="p-price">169 <small>JD</small></span
                  ><a class="btn btn-primary btn-sm" href="login.php"
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
      <a href="home.php">Home</a><a href="products.php">Products</a
      ><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a
      ><a href="account.php">My account</a><a href="wishlist.php">Wishlist</a
      ><a href="login.php">Login</a><a href="register.php">Register</a>
    </nav>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
