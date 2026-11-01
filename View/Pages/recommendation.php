<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../Controllers/RecommendationController.php';

$advisor = new RecommendationController();
$categories = $advisor->categories();
$categoryNames = [];
foreach ($categories as $category) $categoryNames[(int) $category['id']] = (string) $category['name'];
$products = [];
$errorMessage = '';
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$categoryId = 0;
$budgetValue = '';
$partFields = [
    'cpu' => 'CPU / processor', 'gpu' => 'GPU / graphics card',
    'motherboard' => 'Motherboard', 'ram' => 'RAM', 'ssd' => 'SSD / storage',
    'psu' => 'Power supply', 'case' => 'PC case', 'cooler' => 'Cooler',
];
$currentParts = array_fill_keys(array_keys($partFields), '');
$compatibility = null;
$compatibilityError = '';

if (empty($_SESSION['recommendation_csrf_token'])) {
    $_SESSION['recommendation_csrf_token'] = bin2hex(random_bytes(32));
}

if ($submitted) {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['recommendation_csrf_token'], $token)) {
        $errorMessage = 'Your session expired. Refresh the page and try again.';
    } else {
        $rawCategoryId = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $rawBudget = trim((string) ($_POST['budget'] ?? ''));
        $budget = filter_var($rawBudget, FILTER_VALIDATE_FLOAT);
        $submittedParts = $_POST['parts'] ?? [];
        if (!is_array($submittedParts)) $submittedParts = [];
        foreach ($partFields as $slot => $_label) {
            $value = $submittedParts[$slot] ?? '';
            $currentParts[$slot] = is_string($value) ? trim($value) : '';
        }
        $categoryId = is_int($rawCategoryId) ? $rawCategoryId : 0;
        $budgetValue = $rawBudget;

        $tooLongPart = false;
        foreach ($currentParts as $partValue) if (mb_strlen($partValue, 'UTF-8') > 120) $tooLongPart = true;

        if ($categoryId < 1 || !in_array($categoryId, array_map(static fn(array $category): int => (int) $category['id'], $categories), true)) {
            $errorMessage = 'Choose the type of component you want to shop for.';
        } elseif ($budget === false || $budget <= 0 || $budget > 1000000) {
            $errorMessage = 'Enter a valid budget in JD.';
        } elseif ($tooLongPart) {
            $errorMessage = 'Each part name must be 120 characters or less.';
        } else {
            $products = $advisor->recommend($categoryId, (float) $budget);
            if ($products && count(array_filter($currentParts)) > 0) {
                try {
                    $compatibility = $advisor->analyzeCompatibility($categoryNames[$categoryId] ?? 'component', $currentParts, $products);
                    $statuses = ['compatible' => 0, 'unknown' => 1, 'incompatible' => 2];
                    usort($products, static function (array $a, array $b) use ($compatibility, $statuses): int {
                        $resultA = $compatibility['results'][(int) $a['id']] ?? [];
                        $resultB = $compatibility['results'][(int) $b['id']] ?? [];
                        $statusA = $resultA['status'] ?? 'unknown';
                        $statusB = $resultB['status'] ?? 'unknown';
                        return ($statuses[$statusA] ?? 1) <=> ($statuses[$statusB] ?? 1)
                            ?: (($resultB['fit_score'] ?? 0) <=> ($resultA['fit_score'] ?? 0))
                            ?: ($b['recommendation_score'] <=> $a['recommendation_score']);
                    });
                    $products = array_slice($products, 0, 6);
                } catch (Throwable $error) {
                    $compatibilityError = $error->getMessage();
                    $products = array_slice($products, 0, 6);
                }
            } elseif ($products) {
                $compatibilityError = 'Add at least one current part above to run the AI compatibility check.';
            }
        }
    }
}

$selectedCategoryName = $categoryNames[$categoryId] ?? '';
$imageUrl = static function (?string $image): string {
    $image = trim(str_replace('\\', '/', (string) $image));
    if (preg_match('~\Ahttps?://~i', $image)) return $image;
    $image = ltrim((string) preg_replace('~^/?Nexora/~i', '', $image), '/');
    if (!str_starts_with($image, 'Assets/Images/products/') || str_contains($image, '..')) return '';
    return '/Nexora/' . $image;
};
$currentPage = 'recommendation';
$pageTitle = 'PC Parts Recommendation — NEXORA';
$pageStylesheet = 'recommendation.css';
$isAdminPage = false;
$showStoreFooter = true;
require_once __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Layout/user_nav.php';
?>
<main class="container recommendation-page">
  <header class="recommendation-hero">
    <span class="eyebrow">// YOUR BUILD, YOUR BUDGET</span>
    <h1>Find the right <span class="text-grad">next part.</span></h1>
    <p class="muted">Enter the parts already in your PC, choose what you want to buy, and set your budget. Gemini will review candidate compatibility using details found in the NEXORA database.</p>
  </header>

  <form class="recommendation-form card" id="recommendation-form" method="post" action="../Layout/routing.php?page=recommendation">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['recommendation_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="recommendation-fields">
      <label class="recommendation-field">What part are you looking for?
        <select class="input" name="category_id" required>
          <option value="">Choose a component category</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="recommendation-field">Your maximum budget (JD)
        <input class="input" type="number" name="budget" min="1" max="1000000" step="0.01" value="<?= htmlspecialchars($budgetValue, ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. 150" required>
      </label>
    </div>
    <fieldset class="recommendation-parts">
      <legend>What parts do you already have? <span>(all optional)</span></legend>
      <div class="recommendation-part-grid">
        <?php foreach ($partFields as $slot => $label): ?>
          <label class="recommendation-field"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            <input class="input" type="text" name="parts[<?= htmlspecialchars($slot, ENT_QUOTES, 'UTF-8') ?>]" maxlength="120" value="<?= htmlspecialchars($currentParts[$slot], ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. <?= $slot === 'gpu' ? 'RTX 5060' : ($slot === 'ram' ? '16 GB DDR5' : ($slot === 'ssd' ? '1 TB NVMe' : 'model name or spec')) ?>">
          </label>
        <?php endforeach; ?>
      </div>
      <p class="recommendation-hint">These parts do not need to be sold in our store. Enter a model or specification and AI will check it against candidate products and their database specifications.</p>
    </fieldset>
    <button class="btn btn-primary recommendation-submit" id="recommendation-submit" type="submit">
      <span class="recommendation-submit-label">Find my recommendations</span>
      <span class="recommendation-submit-arrow" aria-hidden="true">→</span>
      <span class="recommendation-spinner" aria-hidden="true"></span>
    </button>
    <p class="recommendation-loading-message" id="recommendation-loading-message" role="status" aria-live="polite" hidden>Checking your parts against the store and loading recommendations…</p>
    <?php if ($errorMessage !== ''): ?><p class="recommendation-message recommendation-error" role="alert"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  </form>

  <?php if ($submitted && $errorMessage === ''): ?>
    <section class="recommendation-results" aria-live="polite">
      <div class="recommendation-results-heading">
        <div><span class="eyebrow">// MATCHES IN OUR STORE</span><h2><?= htmlspecialchars($selectedCategoryName, ENT_QUOTES, 'UTF-8') ?> options under <?= number_format((float) $budgetValue, 2) ?> JD</h2></div>
        <a href="../Layout/routing.php?page=products" class="recommendation-browse">Browse full catalog →</a>
      </div>
      <?php if (!$products): ?>
        <div class="recommendation-empty card"><span class="recommendation-empty-icon">◈</span><h3>No in-stock options fit that budget yet.</h3><p class="muted">Try a higher budget or check the full catalog for more products.</p></div>
      <?php else: ?>
        <div class="recommendation-ai-status <?= $compatibility ? 'is-ready' : 'is-pending' ?>">
          <?php if ($compatibility): ?>
            <span class="recommendation-ai-icon" aria-hidden="true">✦</span><div><strong>AI compatibility check</strong><p><?= htmlspecialchars($compatibility['summary'] ?: 'The AI reviewed the candidates against the matching parts in your build.', ENT_QUOTES, 'UTF-8') ?></p></div>
          <?php else: ?>
            <span class="recommendation-ai-icon" aria-hidden="true">!</span><div><strong>Compatibility check not completed</strong><p><?= htmlspecialchars($compatibilityError, ENT_QUOTES, 'UTF-8') ?></p></div>
          <?php endif; ?>
        </div>
        <?php if (count(array_filter($currentParts)) > 0): ?>
          <div class="recommendation-current-setup"><strong>Your current setup</strong><div><?php foreach ($currentParts as $slot => $part): if ($part === '') continue; ?><span><?= htmlspecialchars($partFields[$slot] ?? strtoupper($slot), ENT_QUOTES, 'UTF-8') ?>: <?= htmlspecialchars($part, ENT_QUOTES, 'UTF-8') ?></span><?php endforeach; ?></div></div>
        <?php endif; ?>
        <div class="recommendation-grid">
          <?php foreach ($products as $product): ?>
            <?php
              $image = $imageUrl($product['image'] ?? null);
              $detailUrl = '../Layout/routing.php?page=product&id=' . (int) $product['id'];
              $fit = $compatibility['results'][(int) $product['id']] ?? null;
            ?>
            <article class="recommendation-product card">
              <a class="recommendation-product-image" href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($image !== ''): ?><img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy"><?php else: ?><span aria-hidden="true">◈</span><?php endif; ?>
              </a>
              <div class="recommendation-product-body">
                <span class="recommendation-category"><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                <h3><a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                <?php if ($fit): ?>
                  <span class="compatibility-badge status-<?= htmlspecialchars($fit['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($fit['status']), ENT_QUOTES, 'UTF-8') ?> fit</span>
                  <?php if (!empty($fit['fit_score'])): ?><span class="recommendation-fit-score">AI fit score <?= (int) $fit['fit_score'] ?>%</span><?php endif; ?>
                  <p class="recommendation-fit-reason"><?= htmlspecialchars($fit['reason'], ENT_QUOTES, 'UTF-8') ?></p>
                  <?php if (!empty($fit['checks'])): ?><details class="compatibility-details"><summary>See compatibility checks</summary><ul><?php foreach ($fit['checks'] as $check): ?><li><strong><?= htmlspecialchars($check['component'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars(ucfirst($check['status']), ENT_QUOTES, 'UTF-8') ?>:</strong> <?= htmlspecialchars($check['reason'], ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></details><?php endif; ?>
                <?php elseif (!empty($product['average_rating'])): ?><p class="recommendation-match">★ <?= number_format((float) $product['average_rating'], 1) ?> · <?= (int) $product['review_count'] ?> reviews</p><?php else: ?><p class="recommendation-match">In stock · <?= (int) $product['stock_quantity'] ?> available</p><?php endif; ?>
                <div class="recommendation-product-footer"><strong><?= number_format((float) $product['price'], 2) ?> <small>JD</small></strong><a class="btn btn-primary btn-sm" href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>">View product</a></div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <p class="recommendation-disclaimer">Your current parts can be any model; they do not need to be in the store. Recommendations are limited to products in the store database. The AI uses known hardware information for your parts and the candidate’s stored specifications. “Unknown” means there is not enough information to verify that check.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>
<script>
(() => {
  const form = document.getElementById('recommendation-form');
  const button = document.getElementById('recommendation-submit');
  const label = button.querySelector('.recommendation-submit-label');
  const loadingMessage = document.getElementById('recommendation-loading-message');

  form.addEventListener('submit', () => {
    button.disabled = true;
    button.classList.add('is-loading');
    button.setAttribute('aria-busy', 'true');
    label.textContent = 'Checking compatibility…';
    loadingMessage.hidden = false;
  });
})();
</script>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
