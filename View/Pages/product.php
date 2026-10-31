<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/CartController.php';

$currentPage = 'product';
$pageStylesheet = 'pages.css';
$isAdminPage = false;
$showStoreFooter = true;
$product = null;
$images = [];
$reviews = [];
$rating = ['average_rating' => 0, 'review_count' => 0];
$myReview = null;
$pageError = '';
$reviewError = '';
$reviewRating = 5;
$reviewComment = '';
$isInWishlist = false;
$actionMessage = $_SESSION['product_action_flash'] ?? '';
unset($_SESSION['product_action_flash']);
$actionError = '';
$actionQuantity = 0;
$reviewMessage = $_SESSION['product_review_flash'] ?? '';
unset($_SESSION['product_review_flash']);
$customerLoggedIn = isset($_SESSION['customer_id']) && ($_SESSION['user_role'] ?? null) === 'customer';
if (empty($_SESSION['product_action_csrf_token'])) { $_SESSION['product_action_csrf_token'] = bin2hex(random_bytes(32)); }
if (empty($_SESSION['product_review_csrf_token'])) {
  $_SESSION['product_review_csrf_token'] = bin2hex(random_bytes(32));
}

try {
  $db = new Db();
  $requestedId = filter_var($_GET['id'] ?? $_GET['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
  if ($requestedId) {
    $product = $db->run_query(
      'SELECT p.id, p.name, p.description, p.price, p.stock_quantity, c.name AS category_name
       FROM products AS p INNER JOIN categories AS c ON c.id = p.category_id
       WHERE p.id = :id LIMIT 1',
      ['id' => $requestedId]
    )->fetch() ?: null;
  } elseif (!isset($_GET['id']) && !isset($_GET['product_id'])) {
    $product = $db->run_query(
      'SELECT p.id, p.name, p.description, p.price, p.stock_quantity, c.name AS category_name
       FROM products AS p INNER JOIN categories AS c ON c.id = p.category_id
       ORDER BY p.id DESC LIMIT 1'
    )->fetch() ?: null;
  }

  if ($product) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['add_to_cart', 'add_to_wishlist', 'remove_from_wishlist'], true)) {
      $action = $_POST['action'];
      $actionToken = $_POST['csrf_token'] ?? '';
      if (!is_string($actionToken) || !hash_equals($_SESSION['product_action_csrf_token'], $actionToken)) {
        $actionError = 'Your session expired. Refresh the page and try again.';
      } elseif ($action === 'add_to_cart') {

    if (!$customerLoggedIn) {
        $actionError = 'Please sign in to add products to your cart.';
    } else {
        $actionQuantity = filter_var(
            $_POST['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($actionQuantity === false || $actionQuantity < 1) {
            $actionError = 'Please enter a valid quantity.';
        } else {
            $customerId = (int) $_SESSION['customer_id'];
            $productId = (int) $product['id'];

            $cartController = new CartController($db);

            $added = $cartController->addItem(
                $customerId,
                $productId,
                $actionQuantity
            );

            if ($added) {
                $_SESSION['product_action_flash'] = 'Added to your cart.';

                header(
                    'Location: ../Layout/routing.php?page=product&id='
                    . $productId . '#purchase'
                );
                exit;
            } else {
                $actionError = 'Unable to add product. Check available stock.';
            }
        }
    }
      } elseif (!$customerLoggedIn) {
        $actionError = 'Sign in to save this product.';
      } elseif ($action === 'remove_from_wishlist') {
        $wishlist = $db->run_query('SELECT id FROM wishlists WHERE customer_id = :customer_id LIMIT 1', ['customer_id' => (int) $_SESSION['customer_id']])->fetch();
        if ($wishlist) {
          $db->run_query(
            'DELETE FROM wishlist_items WHERE wishlist_id = :wishlist_id AND product_id = :product_id',
            ['wishlist_id' => (int) $wishlist['id'], 'product_id' => (int) $product['id']]
          );
        }
        $_SESSION['product_action_flash'] = 'Removed from your wishlist.';
        header('Location: ../Layout/routing.php?page=product&id=' . (int) $product['id'] . '#purchase');
        exit;
      } else {
        $customerId = (int) $_SESSION['customer_id'];
        $wishlist = $db->run_query('SELECT id FROM wishlists WHERE customer_id = :customer_id LIMIT 1', ['customer_id' => $customerId])->fetch();
        if (!$wishlist) {
          $db->run_query('INSERT INTO wishlists (customer_id) VALUES (:customer_id)', ['customer_id' => $customerId]);
          $wishlistId = (int) $db->run_query('SELECT LAST_INSERT_ID() AS id')->fetch()['id'];
        } else {
          $wishlistId = (int) $wishlist['id'];
        }
        $alreadySaved = $db->run_query(
          'SELECT 1 FROM wishlist_items WHERE wishlist_id = :wishlist_id AND product_id = :product_id LIMIT 1',
          ['wishlist_id' => $wishlistId, 'product_id' => (int) $product['id']]
        )->fetch();
        if ($alreadySaved) {
          $_SESSION['product_action_flash'] = 'This product is already in your wishlist.';
        } else {
          $db->run_query(
            'INSERT INTO wishlist_items (wishlist_id, product_id) VALUES (:wishlist_id, :product_id)',
            ['wishlist_id' => $wishlistId, 'product_id' => (int) $product['id']]
          );
          $_SESSION['product_action_flash'] = 'Added to your wishlist.';
        }
        header('Location: ../Layout/routing.php?page=product&id=' . (int) $product['id'] . '#purchase');
        exit;
      }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_review') {
      $token = $_POST['csrf_token'] ?? '';
      if (!$customerLoggedIn || !is_string($token) || !hash_equals($_SESSION['product_review_csrf_token'], $token)) {
        $reviewError = 'Unable to delete this review. Refresh the page and try again.';
      } else {
        $db->run_query(
          'DELETE FROM reviews WHERE customer_id = :customer_id AND product_id = :product_id',
          ['customer_id' => (int) $_SESSION['customer_id'], 'product_id' => (int) $product['id']]
        );
        $_SESSION['product_review_flash'] = 'Your review has been deleted.';
        header('Location: ../Layout/routing.php?page=product&id=' . (int) $product['id'] . '#reviews');
        exit;
      }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_review') {
      $reviewRating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
      $reviewComment = is_string($_POST['comment'] ?? null) ? trim($_POST['comment']) : '';
      $token = $_POST['csrf_token'] ?? '';
      if (!is_string($token) || !hash_equals($_SESSION['product_review_csrf_token'], $token)) {
        $reviewError = 'Your session expired. Refresh the page and try again.';
      } elseif (!$customerLoggedIn) {
        $reviewError = 'Sign in to leave a review.';
      } elseif ($reviewRating === false || $reviewRating < 1 || $reviewRating > 5) {
        $reviewError = 'Choose a rating from 1 to 5 stars.';
      } elseif ($reviewComment === '' || mb_strlen($reviewComment) > 2000) {
        $reviewError = 'Write a review of up to 2,000 characters.';
      } else {
        $existing = $db->run_query(
          'SELECT id FROM reviews WHERE customer_id = :customer_id AND product_id = :product_id LIMIT 1',
          ['customer_id' => (int) $_SESSION['customer_id'], 'product_id' => (int) $product['id']]
        )->fetch();
        if ($existing) {
          $db->run_query(
            'UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id AND customer_id = :customer_id',
            ['rating' => $reviewRating, 'comment' => $reviewComment, 'id' => (int) $existing['id'], 'customer_id' => (int) $_SESSION['customer_id']]
          );
          $notice = 'updated';
        } else {
          $db->run_query(
            'INSERT INTO reviews (customer_id, product_id, rating, comment) VALUES (:customer_id, :product_id, :rating, :comment)',
            ['customer_id' => (int) $_SESSION['customer_id'], 'product_id' => (int) $product['id'], 'rating' => $reviewRating, 'comment' => $reviewComment]
          );
          $notice = 'sent';
        }
        $_SESSION['product_review_flash'] = $notice === 'updated' ? 'Your review has been updated.' : 'Thanks! Your review has been added.';
        header('Location: ../Layout/routing.php?page=product&id=' . (int) $product['id'] . '#reviews');
        exit;
      }
    }

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
      'SELECT r.customer_id, r.rating, r.comment, c.first_name, c.last_name
       FROM reviews AS r INNER JOIN customers AS c ON c.id = r.customer_id
       WHERE r.product_id = :product_id ORDER BY (r.customer_id = :customer_id) DESC, r.id DESC LIMIT 6',
      ['product_id' => (int) $product['id'], 'customer_id' => (int) ($_SESSION['customer_id'] ?? 0)]
    )->fetchAll();
    if ($customerLoggedIn) {
      $savedItem = $db->run_query(
        'SELECT 1 FROM wishlists AS w INNER JOIN wishlist_items AS wi ON wi.wishlist_id = w.id
         WHERE w.customer_id = :customer_id AND wi.product_id = :product_id LIMIT 1',
        ['customer_id' => (int) $_SESSION['customer_id'], 'product_id' => (int) $product['id']]
      )->fetch();
      $isInWishlist = (bool) $savedItem;
      $myReview = $db->run_query(
        'SELECT rating, comment FROM reviews WHERE customer_id = :customer_id AND product_id = :product_id LIMIT 1',
        ['customer_id' => (int) $_SESSION['customer_id'], 'product_id' => (int) $product['id']]
      )->fetch() ?: null;
    }
  }
} catch (PDOException $e) {
  error_log($e->getMessage());
  $pageError = 'We could not load product details right now. Please try again later.';
}

$pageTitle = $product ? htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . ' — NEXORA' : 'Product details — NEXORA';
$imageUrl = static function (string $image): string {
  if (preg_match('~\Ahttps?://~i', $image)) return $image;
  return '/Nexora/' . ltrim(str_replace('\\', '/', $image), '/');
};
$averageRating = (float) ($rating['average_rating'] ?? 0);
$reviewCount = (int) ($rating['review_count'] ?? 0);
$stock = (int) ($product['stock_quantity'] ?? 0);
$cartQuantity = $product ? (int) ($_SESSION['cart_items'][(int) $product['id']] ?? 0) : 0;
$availableToAdd = max(0, $stock - $cartQuantity);
$quantityValue = $actionQuantity > 0
  ? min($actionQuantity, max(1, $availableToAdd))
  : 1;
$stars = $reviewCount > 0 ? (int) round($averageRating) : 0;
require_once __DIR__ . '/../Includes/header.php';
?>
<?php require __DIR__ . '/../Layout/user_nav.php'; ?>
<main class="container page-main">
  <?php if ($pageError !== ''): ?>
    <section class="card panel" role="alert"><h1>Product unavailable</h1><p class="muted"><?= htmlspecialchars($pageError, ENT_QUOTES, 'UTF-8') ?></p><a class="btn btn-primary" href="../Layout/routing.php?page=products">Back to products</a></section>
  <?php elseif (!$product): ?>
    <?php http_response_code(404); ?><section class="card panel"><h1>Product not found</h1><p class="muted">This product may have been removed or the link is incomplete.</p><a class="btn btn-primary" href="../Layout/routing.php?page=products">Browse products</a></section>
  <?php else: ?>
    <p class="breadcrumb"><a href="../Layout/routing.php?page=home">Home</a> / <a href="../Layout/routing.php?page=products">Products</a> / <?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></p>
    <div class="product-detail">
      <div class="gallery">
        <div class="product-visual" id="product-visual">
          <?php if ($images): ?><img class="product-main-image" src="<?= htmlspecialchars($imageUrl((string) $images[0]['image']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" id="main-product-image">
          <?php else: ?><div class="product-image-placeholder" id="main-product-placeholder">◈</div><?php endif; ?>
          <span class="badge <?= $stock > 0 ? 'badge-ok' : '' ?>"><?= $stock > 0 ? 'IN STOCK' : 'OUT OF STOCK' ?></span><span class="visual-caption">NEXORA / <?= htmlspecialchars(strtoupper($product['category_name']), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php if (count($images) > 1): ?><div class="thumbs" aria-label="Product images"><?php foreach ($images as $image): ?><button type="button" class="product-thumb" data-image="<?= htmlspecialchars($imageUrl((string) $image['image']), ENT_QUOTES, 'UTF-8') ?>" aria-label="View another product image"><img src="<?= htmlspecialchars($imageUrl((string) $image['image']), ENT_QUOTES, 'UTF-8') ?>" alt=""></button><?php endforeach; ?></div><?php endif; ?>
      </div>
      <section class="product-info">
        <span class="eyebrow"><?= htmlspecialchars(strtoupper($product['category_name']), ENT_QUOTES, 'UTF-8') ?> · NEXORA HARDWARE</span>
        <h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($reviewCount > 0): ?><div class="rating" aria-label="Rated <?= number_format($averageRating, 1) ?> out of 5"><?= str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) ?> <span><?= number_format($averageRating, 1) ?> · <?= $reviewCount ?> <?= $reviewCount === 1 ? 'review' : 'reviews' ?></span></div><?php else: ?><div class="rating">☆☆☆☆☆ <span>No reviews yet</span></div><?php endif; ?>
        <p class="muted product-description-lead"><?= nl2br(htmlspecialchars(trim((string) ($product['description'] ?? '')) !== '' ? $product['description'] : 'A quality addition to your setup. See product details and availability below.', ENT_QUOTES, 'UTF-8')) ?></p>
        <div class="detail-price"><?= number_format((float) $product['price'], 2) ?> <small>JD</small><span class="stock <?= $stock === 0 ? 'stock-out' : '' ?>">● <?= $stock > 0 ? 'In stock · ' . $stock . ' available' : 'Currently unavailable' ?></span></div>
        <div class="spec-strip"><div><small>CATEGORY</small><b><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></b></div><div><small>AVAILABILITY</small><b><?= $stock > 0 ? 'In stock' : 'Out of stock' ?></b></div><div><small>PRODUCT ID</small><b>#<?= (int) $product['id'] ?></b></div></div>
        <div class="purchase-row" id="purchase">
          <form method="post" action="../Layout/routing.php?page=product&amp;id=<?= (int) $product['id'] ?>#purchase" class="product-cart-form">
              <input type="hidden" name="action" value="add_to_cart"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['product_action_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
              <div class="quantity product-quantity"><button type="button" data-quantity-step="-1" aria-label="Decrease quantity">−</button><input type="number" name="quantity" value="<?= $quantityValue ?>" min="1" max="<?= max(1, $availableToAdd) ?>" inputmode="numeric" aria-label="Quantity" <?= $availableToAdd < 1 ? 'disabled' : '' ?>><button type="button" data-quantity-step="1" aria-label="Increase quantity">+</button></div>
              <button class="btn btn-primary" type="submit" <?= $availableToAdd < 1 ? 'disabled' : '' ?>><?= $stock < 1 ? 'Out of stock' : ($availableToAdd < 1 ? 'Maximum in cart' : 'Add to cart') ?></button>
          </form>
          <?php if ($customerLoggedIn): ?><form method="post" action="../Layout/routing.php?page=product&amp;id=<?= (int) $product['id'] ?>#purchase" class="product-wishlist-form"><input type="hidden" name="action" value="<?= $isInWishlist ? 'remove_from_wishlist' : 'add_to_wishlist' ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['product_action_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-ghost btn-sm product-wishlist-button" type="submit"><?= $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' ?></button></form>
          <?php else: ?><a class="btn btn-ghost btn-sm product-wishlist-button" href="../Layout/routing.php?page=login">Sign in to add to wishlist</a><?php endif; ?>
        </div>
        <div class="compat"><span>✦</span><div><b>Need help choosing?</b><p>Explore more hardware and find the right fit for your setup.</p><a href="../Layout/routing.php?page=products">Browse all products →</a></div></div>
        <div class="tabs" role="tablist" aria-label="Product information"><a class="tab-active" href="#description" data-product-tab="description" role="tab" aria-controls="description" aria-selected="true">Description</a><a href="#reviews" data-product-tab="reviews" role="tab" aria-controls="reviews" aria-selected="false">Reviews (<?= $reviewCount ?>)</a></div>
        <div id="description" class="description" role="tabpanel"><h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= nl2br(htmlspecialchars(trim((string) ($product['description'] ?? '')) !== '' ? $product['description'] : 'Product description is not available yet.', ENT_QUOTES, 'UTF-8')) ?></p></div>
      </section>
    </div>
    <section id="reviews" class="card panel product-reviews" role="tabpanel" hidden>
      <div class="row-between"><div class="review-heading-main"><h2>Customer reviews</h2></div><div class="review-header-actions"><span class="muted"><?= $reviewCount ?> total</span><?php if ($customerLoggedIn && !$myReview): ?><button class="btn btn-primary btn-sm review-open-button" type="button" data-review-open>Add review</button><?php endif; ?></div></div>
      <?php if (!$customerLoggedIn): ?><p class="muted">Sign in to rate and review this product.</p><a class="btn btn-primary btn-sm" href="../Layout/routing.php?page=login">Sign in</a><?php endif; ?>
      <?php if (!$reviews): ?><p class="muted">No reviews for this product yet.</p><?php else: ?><div class="review-list">
        <?php foreach ($reviews as $review): ?><article class="product-review"><div class="review-heading"><div><b><?= htmlspecialchars(trim($review['first_name'] . ' ' . $review['last_name']), ENT_QUOTES, 'UTF-8') ?></b><span class="rating-stars"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></span></div><?php if ($customerLoggedIn && (int) $review['customer_id'] === (int) $_SESSION['customer_id']): ?><div class="review-owner-actions"><button class="btn btn-ghost btn-sm" type="button" data-review-open>Edit review</button><form method="post" action="../Layout/routing.php?page=product&amp;id=<?= (int) $product['id'] ?>#reviews" data-delete-review><input type="hidden" name="action" value="delete_review"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['product_review_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-ghost btn-sm review-delete-button" type="submit">Delete review</button></form></div><?php endif; ?></div><?php if (trim((string) ($review['comment'] ?? '')) !== ''): ?><p><?= nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')) ?></p><?php endif; ?></article><?php endforeach; ?>
      </div><?php endif; ?>
    </section>
    <?php if ($customerLoggedIn): ?>
    <div class="modal-overlay review-modal-overlay" id="review-modal" aria-hidden="true"><div class="modal-content review-modal-content" role="dialog" aria-modal="true" aria-labelledby="review-modal-title">
      <h3 id="review-modal-title"><?= $myReview ? 'Edit your review' : 'Add a review' ?></h3>
      <form method="post" action="../Layout/routing.php?page=product&amp;id=<?= (int) $product['id'] ?>#reviews" class="review-form">
        <input type="hidden" name="action" value="submit_review"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['product_review_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <label for="review-rating">Your rating</label><select class="input" id="review-rating" name="rating" required><?php for ($score = 5; $score >= 1; $score--): ?><option value="<?= $score ?>" <?= (int) ($myReview['rating'] ?? $reviewRating) === $score ? 'selected' : '' ?>><?= $score ?> star<?= $score === 1 ? '' : 's' ?> <?= str_repeat('★', $score) ?></option><?php endfor; ?></select>
        <label for="review-comment">Your review</label><textarea class="input" id="review-comment" name="comment" rows="4" maxlength="2000" required placeholder="What did you think of this product?"><?= htmlspecialchars($reviewComment !== '' ? $reviewComment : (string) ($myReview['comment'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        <div class="review-form-actions"><button class="btn btn-primary" type="submit"><?= $myReview ? 'Save changes' : 'Submit review' ?></button><button class="btn btn-ghost btn-sm" type="button" data-review-close>Cancel</button></div>
      </form>
    </div></div>
    <?php endif; ?>
  <?php endif; ?>
</main>
<style>
.product-visual{overflow:hidden}.product-main-image{width:100%;height:100%;object-fit:contain;position:absolute;inset:0;padding:26px;z-index:0}.product-visual>.badge,.product-visual>.visual-caption{z-index:1}.product-image-placeholder{font-size:7rem;color:var(--cyan);text-shadow:0 0 42px rgba(34,211,238,.25)}.product-thumb{width:70px;height:65px;padding:5px;overflow:hidden}.product-thumb img{width:100%;height:100%;object-fit:cover;border-radius:5px}.product-thumb.active{border-color:var(--cyan);box-shadow:0 0 14px rgba(34,211,238,.2)}.product-description-lead{line-height:1.8;white-space:normal}.stock-out{color:#fb7185}.compat a{display:inline-block;margin-top:7px;color:var(--cyan);font-size:.78rem}.product-reviews{margin-top:35px}.product-reviews h2{font:700 1.1rem var(--font-head)}.review-list{display:grid;gap:12px}.product-review{padding:16px 0;border-top:1px solid var(--line)}.review-heading{display:flex;justify-content:space-between;gap:12px}.review-heading b{font-size:.85rem}.review-owner-actions{display:flex;align-items:center;gap:8px}.review-owner-actions form{margin:0}.review-delete-button{color:#fb7185}.review-delete-button:hover{border-color:#fb7185;color:#fff}.rating-stars{color:var(--amber);letter-spacing:2px}.product-review p{color:var(--muted);font-size:.85rem;line-height:1.7;margin:8px 0 0}.review-form-panel{padding:18px;margin:18px 0 10px;border:1px solid var(--line);border-radius:12px;background:rgba(139,92,246,.06)}.review-form-panel h3{font:700 .95rem var(--font-head);margin:0 0 14px}.review-form{display:grid;gap:9px;max-width:620px}.review-form label{margin-top:5px;font-size:.78rem;color:var(--muted)}.review-form select{max-width:220px}.review-form textarea{resize:vertical;min-height:105px}.review-form-actions{display:flex;gap:8px;margin-top:6px}.product-cart-form{display:flex;align-items:center;gap:10px;margin:0}.product-quantity{gap:7px;padding:3px}.product-quantity button{width:32px;height:32px;padding:0;display:grid;place-items:center}.product-quantity input{width:38px;padding:4px 0;border:0;background:transparent;color:var(--text);text-align:center;font:600 .9rem var(--font-body);appearance:textfield}.product-quantity input::-webkit-inner-spin-button,.product-quantity input::-webkit-outer-spin-button{appearance:none;margin:0}.product-quantity button:disabled,.product-cart-form button:disabled{opacity:.5;cursor:not-allowed}.purchase-row{flex-wrap:wrap}.product-wishlist-form{margin:0}.product-cart-form>.btn,.product-wishlist-button{height:68px;min-height:68px;box-sizing:border-box}.product-wishlist-button{white-space:nowrap;padding:14px 22px;font-size:.72rem}.product-action-notice{margin:10px 0}.review-heading-main{display:flex;align-items:center;gap:16px;flex-wrap:wrap}.review-heading-main h2{margin:0}.review-heading-main .status{margin:0}.review-header-actions{display:flex;align-items:center;gap:16px}.review-open-button{margin:0;white-space:nowrap}.review-form-panel[hidden]{display:none}
.product-visual{overflow:hidden}.product-main-image{width:100%;height:100%;object-fit:contain;position:absolute;inset:0;padding:26px;z-index:0}.product-visual>.badge,.product-visual>.visual-caption{z-index:1}.product-image-placeholder{font-size:7rem;color:var(--cyan);text-shadow:0 0 42px rgba(34,211,238,.25)}.product-thumb{width:70px;height:65px;padding:5px;overflow:hidden}.product-thumb img{width:100%;height:100%;object-fit:cover;border-radius:5px}.product-thumb.active{border-color:var(--cyan);box-shadow:0 0 14px rgba(34,211,238,.2)}.product-description-lead{line-height:1.8;white-space:normal}.stock-out{color:#fb7185}.compat a{display:inline-block;margin-top:7px;color:var(--cyan);font-size:.78rem}.product-reviews{margin-top:35px}.product-reviews[hidden]{display:none!important}.product-reviews h2{font:700 1.1rem var(--font-head)}.review-list{display:grid;gap:12px}.product-review{padding:16px 0;border-top:1px solid var(--line)}.review-heading{display:flex;justify-content:space-between;gap:12px}.review-heading b{font-size:.85rem}.review-owner-actions{display:flex;align-items:center;gap:8px}.review-owner-actions form{margin:0}.review-delete-button{color:#fb7185}.review-delete-button:hover{border-color:#fb7185;color:#fff}.rating-stars{color:var(--amber);letter-spacing:2px}.product-review p{color:var(--muted);font-size:.85rem;line-height:1.7;margin:8px 0 0}.review-form{display:grid;gap:9px;max-width:620px}.review-form label{margin-top:5px;font-size:.78rem;color:var(--muted)}.review-form select{max-width:220px}.review-form textarea{resize:vertical;min-height:105px}.review-form-actions{display:flex;gap:8px;margin-top:12px}.review-modal-overlay{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:18px;background:rgba(6,4,14,.84);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}.review-modal-content{width:min(520px,100%);max-height:calc(100vh - 36px);overflow:auto;background:linear-gradient(160deg,var(--panel-2),var(--panel));border:1px solid var(--line);border-radius:16px;padding:30px;box-shadow:0 0 40px rgba(139,92,246,.4);animation:review-modal-pop .3s ease-out}.review-modal-content h3{font:700 1.1rem var(--font-head);margin:0 0 18px}.review-modal-content .review-form{max-width:none}.review-modal-content .review-form select{max-width:none}.review-modal-overlay[aria-hidden="true"]{pointer-events:none}@keyframes review-modal-pop{0%{transform:scale(.9);opacity:0}100%{transform:scale(1);opacity:1}}
.tab-panel-enter{animation:tab-panel-enter .24s ease-out both}@keyframes tab-panel-enter{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:translateY(0)}}@media(prefers-reduced-motion:reduce){.tab-panel-enter{animation:none}}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var productTabs = document.querySelectorAll('[data-product-tab]');
var descriptionPanel = document.getElementById('description');
var reviewsPanel = document.getElementById('reviews');
function showProductTab(tabName, scrollToPanel) {
  var showReviews = tabName === 'reviews';
  reviewsPanel.hidden = !showReviews;
  descriptionPanel.hidden = showReviews;
  var activePanel = showReviews ? reviewsPanel : descriptionPanel;
  activePanel.classList.remove('tab-panel-enter');
  void activePanel.offsetWidth;
  activePanel.classList.add('tab-panel-enter');
  productTabs.forEach(function (tab) {
    var active = tab.dataset.productTab === tabName;
    tab.classList.toggle('tab-active', active);
    tab.setAttribute('aria-selected', active ? 'true' : 'false');
  });
  if (scrollToPanel) {
    (showReviews ? reviewsPanel : descriptionPanel).scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}
productTabs.forEach(function (tab) {
  tab.addEventListener('click', function (event) {
    event.preventDefault();
    var tabName = tab.dataset.productTab;
    showProductTab(tabName, true);
    history.replaceState(null, '', '#' + tabName);
  });
});
showProductTab(window.location.hash === '#reviews' ? 'reviews' : 'description', false);
document.querySelectorAll('[data-quantity-step]').forEach(function (button) {
  var input = button.closest('.product-quantity').querySelector('input[name="quantity"]');
  var controls = button.closest('.product-quantity').querySelectorAll('[data-quantity-step]');
  function updateQuantityButtons() {
    var quantity = parseInt(input.value || '1', 10);
    var min = parseInt(input.min || '1', 10);
    var max = parseInt(input.max || '1', 10);
    controls.forEach(function (control) {
      var step = parseInt(control.dataset.quantityStep, 10);
      control.disabled = input.disabled || (step < 0 ? quantity <= min : quantity >= max);
    });
  }
  button.addEventListener('click', function () {
    var min = parseInt(input.min || '1', 10);
    var max = parseInt(input.max || '1', 10);
    var next = parseInt(input.value || '1', 10) + parseInt(button.dataset.quantityStep, 10);
    input.value = Math.min(max, Math.max(min, next));
    updateQuantityButtons();
  });
  input.addEventListener('input', function () {
    var min = parseInt(input.min || '1', 10);
    var max = parseInt(input.max || '1', 10);
    input.value = Math.min(max, Math.max(min, parseInt(input.value || '1', 10)));
    updateQuantityButtons();
  });
  updateQuantityButtons();
});
function openReviewModal(){var modal=document.getElementById('review-modal');if(!modal)return;modal.style.display='flex';modal.setAttribute('aria-hidden','false');var field=modal.querySelector('select,textarea');if(field)field.focus();}
function closeReviewModal(){var modal=document.getElementById('review-modal');if(modal){modal.style.display='none';modal.setAttribute('aria-hidden','true');}}
document.querySelectorAll('[data-review-open]').forEach(function(button){button.addEventListener('click',openReviewModal);});
document.querySelectorAll('[data-review-close]').forEach(function(button){button.addEventListener('click',closeReviewModal);});
var reviewModal=document.getElementById('review-modal');if(reviewModal){reviewModal.addEventListener('click',function(event){if(event.target===reviewModal)closeReviewModal();});}
document.querySelectorAll('[data-delete-review]').forEach(function(form){form.addEventListener('submit',function(event){event.preventDefault();Swal.fire({title:'Delete your review?',text:'This will permanently remove your review.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',cancelButtonText:'Cancel',reverseButtons:true,buttonsStyling:false,customClass:{popup:'nexora-deactivate-modal',confirmButton:'nexora-confirm-danger',cancelButton:'nexora-cancel-neutral'},background:'#15102a',color:'#f9f5ff'}).then(function(result){if(result.isConfirmed)form.submit();});});});
<?php if ($reviewError !== '' && $customerLoggedIn): ?>openReviewModal();<?php endif; ?>
<?php if ($reviewMessage !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'success',title:<?= json_encode($reviewMessage, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:3000,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});
<?php elseif ($reviewError !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'error',title:<?= json_encode($reviewError, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:4500,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});<?php endif; ?>
<?php if ($actionMessage !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'success',title:<?= json_encode($actionMessage, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:3000,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});
<?php elseif ($actionError !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'error',title:<?= json_encode($actionError, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:4500,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});<?php endif; ?>
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
<?php require_once __DIR__ . '/../Includes/footer.php';
