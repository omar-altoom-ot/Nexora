<?php

require_once __DIR__ . '/../../Config/admin_guard.php';

if (!isset($_SESSION['product_create_csrf_token'])) {
  $_SESSION['product_create_csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Model/ProductRepository.php';
require_once __DIR__ . '/../../Controllers/ProductController.php';
require_once __DIR__ . '/../../Model/CategoryRepository.php';


$db = new Db();

$repository = new ProductRepository($db);
$categoryRepository = new CategoryRepository($db);

$controller = new ProductController(
  $repository,
  $categoryRepository
);

$createErrors = [];
$editErrors = [];

$mode = $_GET['mode'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!in_array($mode, ['create', 'edit', 'deactivate'], true)) {
    http_response_code(405);
    exit('Method not allowed.');
  }

  // 1. CSRF Verification
  if (
    !$controller->isValidCsrfToken(
      $_POST['csrf_token'] ?? null,
      $_SESSION['product_create_csrf_token'] ?? null
    )
  ) {
    http_response_code(403);
    exit('Invalid CSRF token.');
  }

  if ($mode === 'deactivate') {

    $productId = filter_var(
      $_GET['id'] ?? null,
      FILTER_VALIDATE_INT,
      ['options' => ['min_range' => 1]]
    );

    if (!is_int($productId)) {
      http_response_code(404);
      exit('Product not found.');
    }

    try {
      if ($repository->findById($productId) === null) {
        http_response_code(404);
        exit('Product not found.');
      }

      $deactivated = $repository->deactivate($productId);
      $_SESSION['product_success'] = 'Product deactivated successfully.';

      if (!$deactivated) {
        http_response_code(409);
        exit('Product is already inactive.');
      }

      header(
        'Location: /Nexora/View/Layout/routing.php?page=admin-products&deactivated=1',
        true,
        303
      );
      exit;

    } catch (PDOException $e) {
      error_log($e->getMessage());

      http_response_code(500);
      exit('Unable to deactivate product.');
    }
  }

  // 2. Server-side Validation
  $errors = $controller->validateCreateInput($_POST);

  $description = $_POST['description'] ?? null;

  if ($description !== null && !is_string($description)) {
    $errors['description'] = 'Description must be text.';
  }

  // 3. Verify Product ID for Update
  $editId = null;

  if ($mode === 'edit') {
    $editId = filter_var(
      $_GET['id'] ?? null,
      FILTER_VALIDATE_INT,
      ['options' => ['min_range' => 1]]
    );

    if (
      !is_int($editId) ||
      $repository->findById($editId) === null
    ) {
      http_response_code(404);
      exit('Product not found.');
    }
  }

  // 4. Save only after validation
  if ($errors === []) {
    try {

      if ($mode === 'create') {
        $repository->create($_POST);
        $_SESSION['product_success'] =
          'Product created successfully!';
      } else {
        $repository->update($editId, $_POST);
        $_SESSION['product_success'] =
          'Product updated successfully!';
      }

      header(
        'Location: /Nexora/View/Layout/routing.php?page=admin-products&saved=1',
        true,
        303
      );
      exit;

    } catch (PDOException $e) {
      error_log($e->getMessage());
      $errors['general'] = 'Unable to save product.';
    }
  }

  // 5. Display errors in the correct form
  if ($mode === 'create') {
    $createErrors = $errors;
  } else {
    $editErrors = $errors;
  }
}

// ADD FLASH MESSAGE CODE HERE
$successMessage = $_SESSION['product_success'] ?? null;

unset($_SESSION['product_success']);

$products = $controller->index();

$totalCount = count($products);

$activeProducts = array_filter(
  $products,
  fn($product) => (int) $product['is_active'] === 1
);

$activeCount = count($activeProducts);

$lowStockCount = count(array_filter(
  $activeProducts,
  fn($product) =>
    (int) $product['stock_quantity'] > 0 &&
    (int) $product['stock_quantity'] <= 5
));

$outOfStockCount = count(array_filter(
  $activeProducts,
  fn($product) =>
    (int) $product['stock_quantity'] === 0
));

$categories = $categoryRepository->findAll();

$editProduct = null;

if (($_GET['mode'] ?? '') === 'edit') {

  $editId = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
  );

  if ($editId === false) {
    http_response_code(404);
    exit('Product not found.');
  }

  $editProduct = $repository->findById($editId);

  if ($editProduct === null) {
    http_response_code(404);
    exit('Product not found.');
  }
}


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

    <?php if ($editProduct !== null): ?>

      <form class="card panel product-create-form" method="POST"
        action="../Layout/routing.php?page=admin-products&amp;mode=edit&amp;id=<?= (int) $editProduct['id'] ?>">
        <h3>Edit Product #<?= (int) $editProduct['id'] ?></h3>
        <?php foreach ($editErrors as $error): ?>
          <p style="color: #ef4444;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
          </p>
        <?php endforeach; ?>

        <input type="hidden" name="csrf_token"
          value="<?= htmlspecialchars($_SESSION['product_create_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <div class="field">
          <label for="edit-name">Product Name</label>

          <input type="text" id="edit-name" name="name" class="input" maxlength="150"
            value="<?= htmlspecialchars($editProduct['name'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="field">
          <label for="edit-category">Category</label>

          <select id="edit-category" name="category_id" class="input" required>
            <?php foreach ($categories as $category): ?>
              <option value="<?= (int) $category['id'] ?>" <?= (int) $editProduct['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="edit-description">Description</label>

          <textarea id="edit-description" name="description" class="input"
            rows="4"><?= htmlspecialchars((string) ($editProduct['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="product-form-row">
          <div class="field">
            <label for="edit-price">Price (JD)</label>

            <input type="number" id="edit-price" name="price" class="input" min="0.01" step="0.01"
              value="<?= htmlspecialchars((string) $editProduct['price'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="edit-stock">Stock Quantity</label>

            <input type="number" id="edit-stock" name="stock_quantity" class="input" min="0" step="1"
              value="<?= (int) $editProduct['stock_quantity'] ?>" required>
          </div>
        </div>

        <div class="product-form-actions">
          <a href="../Layout/routing.php?page=admin-products" class="product-form-cancel">
            Cancel
          </a>

          <button type="submit" class="btn btn-primary">
            Update Product
          </button>
        </div>
      </form>

    <?php endif; ?>

    <?php if (($_GET['mode'] ?? '') === 'create'): ?>

      <form class="card panel product-create-form" method="POST"
        action="../Layout/routing.php?page=admin-products&amp;mode=create">

        <h3>Create Product</h3>

        <?php foreach ($createErrors as $error): ?>
          <p style="color: #ef4444;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
          </p>
        <?php endforeach; ?>

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

          <button type="submit" class="btn btn-primary">
            Save Product
          </button>

        </div>

      </form>

    <?php endif; ?>
    <div class="grid grid-4 kpi-grid">
      <div class="kpi card"><small>TOTAL PRODUCTS</small><b><?= count($products) ?></b></div>
      <div class="kpi card"><small>ACTIVE LISTINGS</small><b><?= $activeCount ?></b></div>
      <div class="kpi card">
        <small>LOW STOCK</small><b class="warn-text">	<?= $lowStockCount ?></b>
      </div>
      <div class="kpi card"><small>OUT OF STOCK</small><b><?= $outOfStockCount ?></b></div>
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
                    <?php if ((int) $product['is_active'] === 0): ?>

                      <span class="status status-red">Inactive</span>

                    <?php elseif ((int) $product['stock_quantity'] === 0): ?>

                      <span class="status status-red">Out of stock</span>

                    <?php elseif ((int) $product['stock_quantity'] <= 5): ?>
                      <span class="status status-amber">Low stock</span>

                    <?php else: ?>
                      <span class="status status-green">In stock</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a class="btn btn-ghost btn-sm"
                      href="../Layout/routing.php?page=admin-products&amp;mode=edit&amp;id=<?= (int) $product['id'] ?>">
                      Edit
                    </a>

                    <?php if ((int) $product['is_active'] === 1): ?>

                      <form class="js-deactivate-form" method="POST"
                        action="../Layout/routing.php?page=admin-products&amp;mode=deactivate&amp;id=<?= (int) $product['id'] ?>"
                        style="display:inline;">
                        <input type="hidden" name="csrf_token"
                          value="<?= htmlspecialchars($_SESSION['product_create_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                        <button type="submit" class="btn btn-ghost btn-sm">
                          Deactivate
                        </button>
                      </form>

                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  document.querySelectorAll('.js-deactivate-form').forEach((form) => {

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      Swal.fire({
        title: 'Deactivate product?',
        text: 'This product will be hidden from customers. Its data will be preserved.',
        icon: 'warning',
        iconColor: '#F0B66D',

        showCancelButton: true,
        confirmButtonText: 'Deactivate',
        cancelButtonText: 'Keep product',

        reverseButtons: true,
        focusCancel: true,
        buttonsStyling: false,

        backdrop: 'rgba(5, 3, 15, 0.75)',

        customClass: {
          popup: 'nexora-deactivate-modal',
          confirmButton: 'nexora-confirm-danger',
          cancelButton: 'nexora-cancel-neutral'
        }

      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
</script>


<?php if (is_string($successMessage)): ?>
  <script>
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: <?= json_encode(
        $successMessage,
        JSON_HEX_TAG | JSON_HEX_AMP |
        JSON_HEX_APOS | JSON_HEX_QUOT
      ) ?>,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: '#1d152d',
      color: '#f9f5ff'
    });
  </script>
<?php endif; ?>


<?php require_once __DIR__ . '/../Includes/footer.php'; ?>