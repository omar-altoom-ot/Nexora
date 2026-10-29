<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['product_create_csrf_token'])) {
  $_SESSION['product_create_csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Model/ProductRepository.php';
require_once __DIR__ . '/../../Controllers/ProductController.php';
require_once __DIR__ . '/../../Model/CategoryRepository.php';

$db = new Db();
$repository = new ProductRepository($db);
$controller = new ProductController($repository);

$products = $controller->index();

$categoryRepository = new CategoryRepository($db);
$categories = $categoryRepository->findAll();

$currentPage = 'admin-products';
$pageTitle = 'Products — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
<main class="admin-main">
  <header class="admin-top">
    <div>
      <small>ADMIN CONSOLE / <span>PRODUCTS</span></small>
      <h2>Products</h2>
    </div>
    <div class="admin-user">
      <span class="live-dot"></span><span>System live</span><a class="admin-avatar"
        href="../Layout/routing.php?page=admin-profile">AD</a>
    </div>
  </header>
  <div class="admin-content">
    <div class="admin-welcome">
      <div>
        <span class="eyebrow">// CATALOG CONTROL</span>
        <h1>Product <span class="text-grad">inventory.</span></h1>
        <p class="muted">
          Manage listings, stock levels, and catalog details.
        </p>
      </div>
      <a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=admin-products&amp;mode=create">
        ＋ Add product
      </a>
    </div>
    <?php if (($_GET['mode'] ?? '') === 'create'): ?>

      <form class="card panel product-create-form" method="POST"
        action="../Layout/routing.php?page=admin-products&amp;mode=create">

        <h3>Create Product</h3>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token"
          value="<?= htmlspecialchars($_SESSION['product_create_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Product Name -->
        <div class="field">
          <label for="product-name">Product Name</label>

          <input type="text" id="product-name" name="name" class="input" maxlength="150" placeholder="Enter product name"
            required>
        </div>

        <!-- Category -->
        <div class="field">
          <label for="product-category">Category</label>

          <select id="product-category" name="category_id" class="input" required>
            <option value="">Select a category</option>

            <?php foreach ($categories as $category): ?>
              <option value="<?= (int) $category['id'] ?>">
                <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>

          </select>
        </div>

        <!-- Description -->
        <div class="field">
          <label for="product-description">Description</label>

          <textarea id="product-description" name="description" class="input" rows="4"
            placeholder="Enter product description"></textarea>
        </div>

        <!-- Price and Stock -->
        <div class="product-form-row">

          <div class="field">
            <label for="product-price">Price (JD)</label>

            <input type="number" id="product-price" name="price" class="input" min="0.01" step="0.01" placeholder="0.00"
              required>
          </div>

          <div class="field">
            <label for="product-stock">Stock Quantity</label>

            <input type="number" id="product-stock" name="stock_quantity" class="input" min="0" step="1" value="0"
              required>
          </div>

        </div>

        <!-- Actions -->
        <div class="product-form-actions">

          <a href="../Layout/routing.php?page=admin-products" class="product-form-cancel">
            Cancel
          </a>

          <button type="submit" class="btn btn-primary" disabled>
            Save Product
          </button>

        </div>

      </form>

    <?php endif; ?>
    <div class="grid grid-4 kpi-grid">
      <div class="kpi card"><small>TOTAL PRODUCTS</small><b><?= count($products) ?></b></div>
      <div class="kpi card"><small>ACTIVE LISTINGS</small><b>116</b></div>
      <div class="kpi card">
        <small>LOW STOCK</small><b class="warn-text">07</b>
      </div>
      <div class="kpi card"><small>OUT OF STOCK</small><b>05</b></div>
    </div>
    <section class="card panel">
      <div class="table-toolbar">
        <input class="input" placeholder="Search products..." /><select class="input">
          <option>All categories</option>
        </select><button class="btn btn-ghost btn-sm">Filter</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>PRODUCT</th>
              <th>CATEGORY</th>
              <th>PRICE</th>
              <th>STOCK</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)): ?>
              <tr>
                <td colspan="6">
                  No products found. Add your first product.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($products as $product): ?>
                <tr>
                  <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= number_format((float) $product['price'], 2) ?> JD</td>
                  <td><?= (int) $product['stock_quantity'] ?></td>
                  <td>
                    <?php if ((int) $product['stock_quantity'] === 0): ?>
                      <span class="status status-red">Out of stock</span>

                    <?php elseif ((int) $product['stock_quantity'] <= 5): ?>
                      <span class="status status-amber">Low stock</span>

                    <?php else: ?>
                      <span class="status status-green">In stock</span>
                    <?php endif; ?>
                  </td>
                  <td>—</td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>