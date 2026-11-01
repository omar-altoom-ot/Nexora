<?php
require_once __DIR__ . '/../../Controllers/RecommendationController.php';
$builderCategories = [];
$builderProducts = [];
$builderError = null;
try {
    $builderCatalog = new RecommendationController();
    $builderCategories = $builderCatalog->categories();
    foreach ($builderCategories as $category) {
        foreach ($builderCatalog->products((int) $category['id']) as $product) {
            $builderProducts[] = [
                'id' => (int) $product['id'],
                'name' => (string) $product['name'],
                'category' => (string) $category['name'],
                'price' => (float) $product['price'],
                'stock' => (int) $product['stock_quantity'],
                'image' => (string) ($product['image'] ?? ''),
            ];
        }
    }
} catch (Throwable $error) {
    error_log('Builder catalog error: ' . $error->getMessage());
    $builderError = 'Products could not be loaded. Check the database connection.';
}
$currentPage = 'builder';
$pageTitle = 'Build Your PC — NEXORA';
$pageStylesheet = 'builder.css';
$isAdminPage = false;
$showStoreFooter = true;
require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Layout/user_nav.php';
?>
<main class="container builder-page">
  <header class="builder-heading">
    <span class="eyebrow">// NEXORA BUILD LAB</span>
    <h1>Build your <span class="text-grad">dream PC.</span></h1>
    <p class="muted">Choose components, see their 3D models install, and track your budget.</p>
  </header>
  <?php if ($builderError): ?><p role="alert" class="builder-alert"><?= htmlspecialchars($builderError, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <div class="builder-grid">
    <section class="builder-panel builder-picker" aria-label="Choose components">
      <h2>Components</h2>
      <label for="builder-category">Category</label>
      <select id="builder-category" class="input"><option value="">Choose a category</option></select>
      <label for="builder-product">Product</label>
      <select id="builder-product" class="input" disabled><option value="">Choose a category first</option></select>
      <div id="builder-preview" class="builder-preview">Select a product to preview it.</div>
      <button id="builder-install" type="button" class="btn btn-primary" disabled>Install component</button>
      <p id="builder-status" role="status" aria-live="polite" class="builder-status">Select a case first for the best visual experience.</p>
    </section>
    <section class="builder-panel builder-stage" aria-label="Animated PC assembly preview">
      <div class="builder-stage-head"><h2>Assembly Studio</h2><span>INTERACTIVE 3D</span></div>
      <div class="builder-scene builder-scene-3d" id="builder-scene">
        <div class="builder-product-name" id="builder-part-title">Choose a component</div>
        <div id="builder-viewport" class="builder-viewport"></div>
        <div class="builder-3d-hint">Drag to rotate · Scroll to zoom</div>
      </div>
      <p class="builder-disclaimer">Visual installation is illustrative, not to scale. Compatibility checks are preliminary and depend on available product specifications.</p>
    </section>
    <aside class="builder-panel builder-summary">
      <h2>Your build</h2>
      <div id="builder-selected" class="builder-selected"><p class="muted">No components selected yet.</p></div>
      <div class="builder-total"><span>Estimated total</span><strong id="builder-total">0.00 JD</strong></div>
      <button type="button" id="builder-reset" class="btn btn-ghost">Reset build</button>
      <section class="builder-compat" aria-label="Compatibility checker">
        <h3>Compatibility Checker</h3>
        <div id="builder-compat-status" class="builder-compat-status" role="status" aria-live="polite">Choose components to check compatibility.</div>
        <div id="builder-compat-checks" class="builder-compat-checks"></div>
        <p class="builder-disclaimer">Unknown means specifications are missing. Passing checks do not guarantee full system compatibility.</p>
      </section>
    </aside>
  </div>
</main>
<script id="builder-products-json" type="application/json"><?= json_encode($builderProducts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?></script>
<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.170.0/build/three.module.js","three/addons/":"https://cdn.jsdelivr.net/npm/three@0.170.0/examples/jsm/"}}</script>
<script type="module" src="../js/builder.js"></script>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
