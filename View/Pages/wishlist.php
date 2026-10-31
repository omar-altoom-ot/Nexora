<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CartController.php';

$currentPage = 'wishlist';
$pageTitle = 'Wishlist — NEXORA';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = true;
$customerLoggedIn = isset($_SESSION['customer_id']) && ($_SESSION['user_role'] ?? null) === 'customer';
$customerId = $customerLoggedIn ? (int) $_SESSION['customer_id'] : 0;
$items = [];
$pageError = '';
$actionError = '';
$notice = $_SESSION['wishlist_flash'] ?? '';
unset($_SESSION['wishlist_flash']);
if (empty($_SESSION['wishlist_csrf_token'])) {
  $_SESSION['wishlist_csrf_token'] = bin2hex(random_bytes(32));
}

try {
  $db = new Db();
  if ($customerLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['wishlist_csrf_token'], $token)) {
      $actionError = 'Your session expired. Refresh the page and try again.';
    } else {
      $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
      $action = $_POST['action'] ?? '';
      $wishlist = $db->run_query(
        'SELECT id FROM wishlists WHERE customer_id = :customer_id LIMIT 1',
        ['customer_id' => $customerId]
      )->fetch();
      if (!$productId || !$wishlist) {
        $actionError = 'This wishlist item could not be found.';
      } elseif ($action === 'remove') {
        $db->run_query(
          'DELETE FROM wishlist_items WHERE wishlist_id = :wishlist_id AND product_id = :product_id',
          ['wishlist_id' => (int) $wishlist['id'], 'product_id' => $productId]
        );
        $_SESSION['wishlist_flash'] = 'Product removed from your wishlist.';
        header('Location: ../Layout/routing.php?page=wishlist');
        exit;
      } elseif ($action === 'add_to_cart') {
        $cartController = new CartController($db);
        if ($cartController->addItem($customerId, (int) $productId, 1)) {
          $_SESSION['wishlist_flash'] = 'Product added successfully to your cart.';
          header('Location: ../Layout/routing.php?page=wishlist');
          exit;
        } else {
          $actionError = 'Could not add the product. Check the available stock.';
        }
      } else {
        $actionError = 'Invalid wishlist action.';
      }
    }
  }

  if ($customerLoggedIn) {
    $items = $db->run_query(
      'SELECT p.id, p.name, p.description, p.price, p.stock_quantity,
              c.name AS category_name,
              (SELECT pi.image FROM product_images AS pi WHERE pi.product_id = p.id ORDER BY pi.id ASC LIMIT 1) AS image,
              (SELECT AVG(r.rating) FROM reviews AS r WHERE r.product_id = p.id) AS average_rating,
              (SELECT COUNT(*) FROM reviews AS r WHERE r.product_id = p.id) AS review_count
       FROM wishlists AS w
       INNER JOIN wishlist_items AS wi ON wi.wishlist_id = w.id
       INNER JOIN products AS p ON p.id = wi.product_id
       INNER JOIN categories AS c ON c.id = p.category_id
       WHERE w.customer_id = :customer_id
       ORDER BY p.name ASC',
      ['customer_id' => $customerId]
    )->fetchAll();
  }
} catch (PDOException $e) {
  error_log($e->getMessage());
  $pageError = 'Unable to load your wishlist right now. Please try again later.';
}

$imageUrl = static function (string $image): string {
  if (preg_match('~\Ahttps?://~i', $image)) return $image;
  return '/Nexora/' . ltrim(str_replace('\\', '/', $image), '/');
};
require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/user_nav.php'; ?>
<main class="container page-main wishlist-page">
  <span class="eyebrow">// SAVED FOR LATER</span>
  <h1 class="page-title">Your <span class="text-grad">wishlist.</span></h1>
  <p class="muted">Keep your favorite gear close and ready for your next build.</p>

  <?php if ($pageError !== ''): ?>
    <section class="card panel" role="alert"><p><?= htmlspecialchars($pageError, ENT_QUOTES, 'UTF-8') ?></p></section>
  <?php elseif (!$customerLoggedIn): ?>
    <section class="card panel wishlist-empty"><div class="wishlist-empty-icon">♡</div><h2>Your wishlist is waiting</h2><p class="muted">Sign in to see the products you have saved.</p><a class="btn btn-primary" href="../Layout/routing.php?page=login">Sign in</a></section>
  <?php elseif (!$items): ?>
    <section class="card panel wishlist-empty"><div class="wishlist-empty-icon">♡</div><h2>Your wishlist is empty</h2><p class="muted">Tap the heart on a product to save it here.</p><a class="btn btn-primary" href="../Layout/routing.php?page=products">Explore products</a></section>
  <?php else: ?>
    <div class="grid grid-4 product-grid wishlist-grid">
      <?php foreach ($items as $item): $stock = (int) $item['stock_quantity']; $average = (float) ($item['average_rating'] ?? 0); $count = (int) $item['review_count']; $image = trim((string) ($item['image'] ?? '')); ?>
        <article class="card p-card wishlist-card">
          <a class="p-img wishlist-product-image" href="../Layout/routing.php?page=product&amp;id=<?= (int) $item['id'] ?>">
            <?php if ($image !== ''): ?><img src="<?= htmlspecialchars($imageUrl($image), ENT_QUOTES, 'UTF-8') ?>" alt="" onerror="this.hidden=true; this.nextElementSibling.hidden=false"><span class="wishlist-placeholder" hidden>◈</span>
            <?php else: ?><span class="wishlist-placeholder">◈</span><?php endif; ?>
            <span class="badge <?= $stock > 0 ? 'badge-ok' : '' ?>"><?= $stock > 0 ? 'IN STOCK' : 'OUT OF STOCK' ?></span>
          </a>
          <div class="p-body">
            <span class="p-cat"><?= htmlspecialchars(strtoupper($item['category_name']), ENT_QUOTES, 'UTF-8') ?></span>
            <a class="p-name" href="../Layout/routing.php?page=product&amp;id=<?= (int) $item['id'] ?>"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></a>
            <?php if ($count > 0): ?><span class="p-stars"><?= str_repeat('★', (int) round($average)) . str_repeat('☆', 5 - (int) round($average)) ?> <small><?= number_format($average, 1) ?> (<?= $count ?>)</small></span><?php else: ?><span class="p-stars wishlist-no-rating">No reviews yet</span><?php endif; ?>
            <div class="p-foot"><span class="p-price"><?= number_format((float) $item['price'], 2) ?> <small>JD</small></span>
              <form method="post" action="../Layout/routing.php?page=wishlist" class="wishlist-cart-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['wishlist_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="add_to_cart"><input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                <button class="btn btn-primary btn-sm" type="submit" <?= $stock < 1 ? 'disabled' : '' ?>><?= $stock > 0 ? 'Add to cart' : 'Out of stock' ?></button>
              </form>
            </div>
            <form method="post" action="../Layout/routing.php?page=wishlist" class="wishlist-remove-form" data-wishlist-remove>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['wishlist_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
              <button class="wishlist-remove" type="submit">Remove from wishlist</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<style>
.wishlist-page>.muted{margin:-6px 0 25px}.wishlist-grid{grid-template-columns:repeat(auto-fill,minmax(min(100%,260px),1fr));gap:22px;margin-top:24px}.wishlist-card .p-body{padding:17px;gap:10px}.wishlist-card .p-name{min-height:2.7em}.wishlist-card .p-foot{margin-top:6px}.wishlist-card{min-width:0;border-color:rgba(139,92,246,.28);transition:transform .2s,border-color .2s,box-shadow .2s}.wishlist-card:hover{transform:translateY(-4px);border-color:rgba(34,211,238,.42);box-shadow:0 16px 42px rgba(0,0,0,.32)}.wishlist-product-image{position:relative;isolation:isolate;overflow:hidden;height:190px;min-height:190px;flex:none;background:radial-gradient(circle at 50% 42%,rgba(139,92,246,.34),transparent 68%),var(--panel-2)}.wishlist-product-image img{position:relative;z-index:1;width:100%;height:100%;max-width:100%;object-fit:contain;object-position:center;padding:32px 18px 18px;display:block;border:0}.wishlist-product-image img[hidden]{display:none}.wishlist-product-image .wishlist-placeholder[hidden]{display:none}.wishlist-product-image .wishlist-placeholder{position:absolute;inset:0;width:100%;height:100%;display:grid;place-items:center;font-size:4rem;color:var(--cyan);text-shadow:0 0 25px rgba(34,211,238,.25)}.wishlist-product-image .badge{position:absolute;z-index:2;top:12px;inset-inline-start:12px;background:rgba(8,7,20,.9);box-shadow:0 4px 16px rgba(0,0,0,.3)}.wishlist-placeholder{font-size:4rem;color:var(--cyan)}.wishlist-no-rating{color:var(--muted);letter-spacing:0}.wishlist-cart-form,.wishlist-remove-form{margin:0}.wishlist-cart-form button:disabled{opacity:.55;cursor:not-allowed}.wishlist-remove-form{margin-top:10px}.wishlist-remove{padding:4px 0;border:0;background:transparent;color:#fb7185;font:500 .72rem var(--font-body);cursor:pointer}.wishlist-remove:hover{text-decoration:underline}.wishlist-empty{max-width:620px;margin:32px auto;text-align:center;padding:42px 24px}.wishlist-empty-icon{width:68px;height:68px;margin:0 auto 18px;border:1px solid rgba(236,72,153,.35);border-radius:20px;display:grid;place-items:center;color:#fb7185;background:rgba(236,72,153,.08);font-size:2rem}.wishlist-empty h2{font:700 1.2rem var(--font-head);margin-bottom:10px}.wishlist-empty p{margin-bottom:20px}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('[data-wishlist-remove]').forEach(function(form){form.addEventListener('submit',function(event){event.preventDefault();Swal.fire({title:'Remove from wishlist?',text:'This product will be removed from your saved items.',icon:'warning',showCancelButton:true,confirmButtonText:'Remove',cancelButtonText:'Cancel',reverseButtons:true,buttonsStyling:false,customClass:{popup:'nexora-deactivate-modal',confirmButton:'nexora-confirm-danger',cancelButton:'nexora-cancel-neutral'},background:'#15102a',color:'#f9f5ff'}).then(function(result){if(result.isConfirmed)form.submit();});});});
<?php if ($notice !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'success',title:<?= json_encode($notice, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:3000,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});
<?php elseif ($actionError !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'error',title:<?= json_encode($actionError, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:4500,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});<?php endif; ?>
</script>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
