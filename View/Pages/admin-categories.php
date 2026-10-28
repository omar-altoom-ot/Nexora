<?php
$currentPage = 'admin-categories';
$pageTitle = 'Categories — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>CATEGORIES</span></small>
          <h2>Categories</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="admin-profile.php">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// ORGANIZE THE ARENA</span>
            <h1>Product <span class="text-grad">categories.</span></h1>
            <p class="muted">Keep your catalog clear and easy to explore.</p>
          </div>
          <button class="btn btn-primary btn-sm">＋ New category</button>
        </div>
        <div class="grid grid-3 category-admin-grid">
          <article class="card category-admin">
            <div class="category-icon">🖥️</div>
            <div>
              <h3>Gaming PCs</h3>
              <p>Prebuilt systems and custom rigs</p>
              <small>16 PRODUCTS</small>
            </div>
            <button>•••</button>
          </article>
          <article class="card category-admin">
            <div class="category-icon">⚡</div>
            <div>
              <h3>Components</h3>
              <p>CPU · GPU · memory · storage</p>
              <small>42 PRODUCTS</small>
            </div>
            <button>•••</button>
          </article>
          <article class="card category-admin">
            <div class="category-icon">💻</div>
            <div>
              <h3>Laptops</h3>
              <p>Gaming and creator laptops</p>
              <small>22 PRODUCTS</small>
            </div>
            <button>•••</button>
          </article>
          <article class="card category-admin">
            <div class="category-icon">🎧</div>
            <div>
              <h3>Peripherals</h3>
              <p>Keyboards, mice and audio</p>
              <small>31 PRODUCTS</small>
            </div>
            <button>•••</button>
          </article>
          <article class="card category-admin">
            <div class="category-icon">🌐</div>
            <div>
              <h3>Networking</h3>
              <p>Routers and network gear</p>
              <small>09 PRODUCTS</small>
            </div>
            <button>•••</button>
          </article>
          <article class="card category-admin add-category">
            <div class="category-icon">＋</div>
            <div>
              <h3>Add a category</h3>
              <p>Create a new catalog group</p>
            </div>
          </article>
        </div>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>
