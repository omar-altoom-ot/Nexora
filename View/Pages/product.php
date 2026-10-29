<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../Config/DBconnection.php';

$currentPage = 'product';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = true;
$product = null;
$images = [];
$reviews = [];
$rating = ['average_rating' => 0, 'review_count' => 0];
$pageError = '';

try {
  $db = new Db();
  $requestedId = filter_var($_GET['id'] ?? $_GET['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
  if ($requestedId) {
    $product = $db->run_query(
      'SELECT p.id, p.name, p.description, p.price, p.stock_quantity, c.name AS category_name
       FROM products AS p
       INNER JOIN categories AS c ON c.id = p.category_id
       WHERE p.id = :id LIMIT 1',
      ['id' => $requestedId]
    )->fetch() ?: null;
  } elseif (!isset($_GET['id']) && !isset($_GET['product_id'])) {
    // Keep the page previewable before product cards begin passing their IDs.
    $product = $db->run_query(
      'SELECT p.id, p.name, p.description, p.price, p.stock_quantity, c.name AS category_name
       FROM products AS p
       INNER JOIN categories AS c ON c.id = p.category_id
       ORDER BY p.id DESC LIMIT 1'
    )->fetch() ?: null;
  }

  if ($product) {
    $images = $db->run_query(
      'SELECT image FROM product_images WHERE product_id = :product_id ORDER BY id ASC',
      ['product_id' => (int) $product['id']]
    )->fetchAll();
    $rating = $db->run_query(
      'SELECT COALESCE(AVG(rating), 0) AS average_rating, COUNT(*) AS review_count
       FROM reviews WHERE product_id = :product_id',
      ['product_id' => (int) $product['id']]
    )->fetch() ?: $rating;
    $reviews = $db->run_query(
      'SELECT r.rating, r.comment, c.first_name, c.last_name
       FROM reviews AS r INNER JOIN customers AS c ON c.id = r.customer_id
       WHERE r.product_id = :product_id ORDER BY r.id DESC LIMIT 6',
      ['product_id' => (int) $product['id']]
    )->fetchAll();
  }
} catch (PDOException $e) {
  error_log($e->getMessage());
  $pageError = 'We could not load product details right now. Please try again later.';
}

$pageTitle = $product ? htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . ' — NEXORA' : 'Product details — NEXORA';
$customerLoggedIn = isset($_SESSION['customer_id']) && ($_SESSION['user_role'] ?? null) === 'customer';
$imageUrl = static function (string $image): string {
  if (preg_match('~\Ahttps?://~i', $image)) return $image;
  return '/Nexora/' . ltrim(str_replace('\\', '/', $image), '/');
};
$averageRating = (float) ($rating['average_rating'] ?? 0);
$reviewCount = (int) ($rating['review_count'] ?? 0);
$stock = (int) ($product['stock_quantity'] ?? 0);
$stars = $reviewCount > 0 ? (int) round($averageRating) : 0;
require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/user_nav.php'; ?>
<main class="container page-main">
  <?php if ($pageError !== ''): ?>
    <section class="card panel" role="alert"><h1>Product unavailable</h1><p class="muted"><?= htmlspecialchars($pageError, ENT_QUOTES, 'UTF-8') ?></p><a class="btn btn-primary" href="../Layout/routing.php?page=products">Back to products</a></section>
  <?php elseif (!$product): ?>
    <?php http_response_code(404); ?>
    <section class="card panel"><h1>Product not found</h1><p class="muted">This product may have been removed or the link is incomplete.</p><a class="btn btn-primary" href="../Layout/routing.php?page=products">Browse products</a></section>
  <?php else: ?>
    <p class="breadcrumb"><a href="../Layout/routing.php?page=home">Home</a> / <a href="../Layout/routing.php?page=products">Products</a> / <?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></p>
    <div class="product-detail">
      <div class="gallery">
        <div class="product-visual" id="product-visual">
          <?php if ($images): ?><img class="product-main-image" src="<?= htmlspecialchars($imageUrl((string) $images[0]['image']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" id="main-product-image">
          <?php else: ?><div class="product-image-placeholder" id="main-product-placeholder">◈</div><?php endif; ?>
          <span class="badge <?= $stock > 0 ? 'badge-ok' : '' ?>"><?= $stock > 0 ? 'IN STOCK' : 'OUT OF STOCK' ?></span>
          <span class="visual-caption">NEXORA / <?= htmlspecialchars(strtoupper($product['category_name']), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php if (count($images) > 1): ?><div class="thumbs" aria-label="Product images">
          <?php foreach ($images as $image): ?><button type="button" class="product-thumb" data-image="<?= htmlspecialchars($imageUrl((string) $image['image']), ENT_QUOTES, 'UTF-8') ?>" aria-label="View another product image"><img src="<?= htmlspecialchars($imageUrl((string) $image['image']), ENT_QUOTES, 'UTF-8') ?>" alt=""></button><?php endforeach; ?>
        </div><?php endif; ?>
      </div>
      <section class="product-info">
        <span class="eyebrow"><?= htmlspecialchars(strtoupper($product['category_name']), ENT_QUOTES, 'UTF-8') ?> · NEXORA HARDWARE</span>
        <h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($reviewCount > 0): ?><div class="rating" aria-label="Rated <?= number_format($averageRating, 1) ?> out of 5"><?= str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) ?> <span><?= number_format($averageRating, 1) ?> · <?= $reviewCount ?> <?= $reviewCount === 1 ? 'review' : 'reviews' ?></span></div>
        <?php else: ?><div class="rating">☆☆☆☆☆ <span>No reviews yet</span></div><?php endif; ?>
        <p class="muted product-description-lead"><?= nl2br(htmlspecialchars(trim((string) ($product['description'] ?? '')) !== '' ? $product['description'] : 'A quality addition to your setup. See product details and availability below.', ENT_QUOTES, 'UTF-8')) ?></p>
        <div class="detail-price"><?= number_format((float) $product['price'], 2) ?> <small>JD</small><span class="stock <?= $stock === 0 ? 'stock-out' : '' ?>">● <?= $stock > 0 ? 'In stock · ' . $stock . ' available' : 'Currently unavailable' ?></span></div>
        <div class="spec-strip"><div><small>CATEGORY</small><b><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></b></div><div><small>AVAILABILITY</small><b><?= $stock > 0 ? 'In stock' : 'Out of stock' ?></b></div><div><small>PRODUCT ID</small><b>#<?= (int) $product['id'] ?></b></div></div>
        <div class="purchase-row">
          <?php if ($customerLoggedIn): ?><a class="btn btn-primary" href="../Layout/routing.php?page=cart&amp;product_id=<?= (int) $product['id'] ?>" <?= $stock < 1 ? 'aria-disabled="true"' : '' ?>><?= $stock > 0 ? 'Add to cart' : 'Out of stock' ?></a>
          <?php else: ?><a class="btn btn-primary" href="../Layout/routing.php?page=login">Sign in to add to cart</a><?php endif; ?>
          <a class="icon-btn wishlist" href="../Layout/routing.php?page=<?= $customerLoggedIn ? 'wishlist' : 'login' ?>" aria-label="Add to wishlist">♡</a>
        </div>
        <div class="compat"><span>✦</span><div><b>Need help choosing?</b><p>Explore more hardware and find the right fit for your setup.</p><a href="../Layout/routing.php?page=products">Browse all products →</a></div></div>
        <div class="tabs"><a class="tab-active" href="#description">Description</a><a href="#reviews">Reviews (<?= $reviewCount ?>)</a></div>
        <div id="description" class="description"><h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= nl2br(htmlspecialchars(trim((string) ($product['description'] ?? '')) !== '' ? $product['description'] : 'Product description is not available yet.', ENT_QUOTES, 'UTF-8')) ?></p></div>
      </section>
    </div>
    <section id="reviews" class="card panel product-reviews"><div class="row-between"><h2>Customer reviews</h2><span class="muted"><?= $reviewCount ?> total</span></div>
      <?php if (!$reviews): ?><p class="muted">No reviews for this product yet.</p><?php else: ?><div class="review-list">
        <?php foreach ($reviews as $review): ?><article class="product-review"><div class="review-heading"><b><?= htmlspecialchars(trim($review['first_name'] . ' ' . $review['last_name']), ENT_QUOTES, 'UTF-8') ?></b><span class="rating-stars"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></span></div><?php if (trim((string) ($review['comment'] ?? '')) !== ''): ?><p><?= nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')) ?></p><?php endif; ?></article><?php endforeach; ?>
      </div><?php endif; ?>
    </section>
  <?php endif; ?>
</main>
<style>
.product-visual{overflow:hidden}.product-main-image{width:100%;height:100%;object-fit:contain;position:absolute;inset:0;padding:26px;z-index:0}.product-visual>.badge,.product-visual>.visual-caption{z-index:1}.product-image-placeholder{font-size:7rem;color:var(--cyan);text-shadow:0 0 42px rgba(34,211,238,.25)}.product-thumb{width:70px;height:65px;padding:5px;overflow:hidden}.product-thumb img{width:100%;height:100%;object-fit:cover;border-radius:5px}.product-thumb.active{border-color:var(--cyan);box-shadow:0 0 14px rgba(34,211,238,.2)}.product-description-lead{line-height:1.8;white-space:normal}.stock-out{color:#fb7185}.compat a{display:inline-block;margin-top:7px;color:var(--cyan);font-size:.78rem}.product-reviews{margin-top:35px}.product-reviews h2{font:700 1.1rem var(--font-head)}.review-list{display:grid;gap:12px}.product-review{padding:16px 0;border-top:1px solid var(--line)}.review-heading{display:flex;justify-content:space-between;gap:12px}.review-heading b{font-size:.85rem}.rating-stars{color:var(--amber);letter-spacing:2px}.product-review p{color:var(--muted);font-size:.85rem;line-height:1.7;margin:8px 0 0}
</style>
<script>
document.querySelectorAll('.product-thumb').forEach(function (thumb) {
  thumb.addEventListener('click', function () {
    var mainImage = document.getElementById('main-product-image');
    if (!mainImage) return;
    mainImage.src = thumb.dataset.image;
    document.querySelectorAll('.product-thumb').forEach(function (item) { item.classList.remove('active'); });
    thumb.classList.add('active');
  });
});
</script>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
