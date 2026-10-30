<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../Controllers/CompareController.php';

$compare = new CompareController();
if (empty($_SESSION['compare_csrf_token'])) $_SESSION['compare_csrf_token'] = bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) throw new InvalidArgumentException('Invalid request.');
        $token = $payload['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['compare_csrf_token'], $token)) {
            http_response_code(403);
            throw new RuntimeException('Your session expired. Refresh the page and try again.');
        }
        $action = $_GET['action'];
        if ($action === 'products') {
            $categoryId = filter_var($payload['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$categoryId) throw new InvalidArgumentException('Choose a category first.');
            echo json_encode(['ok' => true, 'products' => $compare->products((int) $categoryId)], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($action !== 'compare') throw new InvalidArgumentException('Unknown action.');

        $categoryId = filter_var($payload['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $firstId = filter_var($payload['first_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $secondId = filter_var($payload['second_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$categoryId || !$firstId || !$secondId) throw new InvalidArgumentException('Choose a category and two products.');
        $purpose = is_string($payload['purpose'] ?? null) ? trim($payload['purpose']) : '';
        $budget = is_string($payload['budget'] ?? null) ? trim($payload['budget']) : '';
        $priority = is_string($payload['priority'] ?? null) ? trim($payload['priority']) : '';
        if (mb_strlen($purpose) > 300 || mb_strlen($budget) > 100) throw new InvalidArgumentException('Please shorten your requirements.');

        [$first, $second] = $compare->productPair((int) $categoryId, (int) $firstId, (int) $secondId);
        $analysis = $compare->aiComparison($first, $second, ['purpose' => $purpose ?: 'General use', 'budget_jd' => $budget ?: 'Not specified', 'priority' => $priority ?: 'Balanced']);
        echo json_encode(['ok' => true, 'products' => [$first, $second], 'analysis' => $analysis], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (Throwable $error) {
        if (http_response_code() < 400) http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

$categories = $compare->categories();
$currentPage = 'compare';
$pageTitle = 'Compare components — NEXORA';
$pageStylesheet = 'compare.css';
$isAdminPage = false;
$showStoreFooter = true;
require_once __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Layout/user_nav.php';
?>
<main class="container compare-page">
  <section class="compare-heading">
    <span class="eyebrow">// SMART HARDWARE MATCH</span>
    <h1>Compare with <span class="text-grad">confidence.</span></h1>
    <p class="muted">Choose a category and two products. AI weighs your needs against real product information from our store.</p>
  </section>

  <section class="card compare-setup">
    <label class="compare-field category-field">Category
      <select class="input" id="category-select">
        <option value="">Choose a category</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= (int) $category['id'] ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <div class="compare-products">
      <label class="compare-field">Product one
        <select class="input" id="first-product" disabled><option value="">Choose a category first</option></select>
      </label>
      <div class="versus">VS</div>
      <label class="compare-field">Product two
        <select class="input" id="second-product" disabled><option value="">Choose a category first</option></select>
      </label>
    </div>
    <div class="compare-needs">
      <label class="compare-field">Main use
        <select class="input" id="purpose">
          <option value="Gaming">Gaming</option><option value="Programming">Programming</option>
          <option value="Video editing">Video editing</option><option value="3D design">3D design</option>
          <option value="Office and everyday use">Office and everyday use</option><option value="General use">General use</option>
        </select>
      </label>
      <label class="compare-field">Budget (JD)
        <input class="input" id="budget" type="number" min="0" step="0.01" placeholder="Optional">
      </label>
      <label class="compare-field">Top priority
        <select class="input" id="priority">
          <option value="Balanced">Balanced</option><option value="Performance">Performance</option>
          <option value="Price and value">Price and value</option><option value="Power efficiency">Power efficiency</option>
          <option value="Customer ratings">Customer ratings</option>
        </select>
      </label>
    </div>
    <button class="btn btn-primary compare-submit" id="compare-button" type="button">Compare with AI</button>
    <p class="compare-status" id="compare-status" role="status" aria-live="polite"></p>
  </section>

  <section class="card compare-results" id="compare-results" hidden>
    <div class="compare-results-head"><div><span class="eyebrow">// SIDE BY SIDE</span><h2>Product comparison</h2></div><span class="winner-pill" id="winner-pill" hidden></span></div>
    <div class="compare-table-wrap"><table class="compare-table"><thead id="compare-thead"></thead><tbody id="compare-tbody"></tbody></table></div>
    <section class="ai-analysis" id="ai-analysis">
      <span class="eyebrow">// AI RECOMMENDATION</span><h2 id="ai-summary"></h2><p id="winner-reason"></p>
      <h3>Trade-offs</h3><ul id="tradeoffs"></ul>
      <p class="data-note">Recommendations use only product information and reviews currently available in the store. Missing specifications are marked as unavailable.</p>
    </section>
  </section>
</main>
<script>
(() => {
  const endpoint = '../Layout/routing.php?page=compare';
  const csrf = <?= json_encode($_SESSION['compare_csrf_token']) ?>;
  const category = document.getElementById('category-select');
  const selectors = [document.getElementById('first-product'), document.getElementById('second-product')];
  const button = document.getElementById('compare-button');
  const status = document.getElementById('compare-status');
  const results = document.getElementById('compare-results');
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const post = async (action, data) => {
    const response = await fetch(`${endpoint}&action=${action}`, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({...data, csrf_token:csrf})});
    const json = await response.json();
    if (!response.ok || !json.ok) throw new Error(json.error || 'Something went wrong.');
    return json;
  };
  category.addEventListener('change', async () => {
    results.hidden = true; status.textContent = '';
    selectors.forEach(select => {select.disabled=true; select.innerHTML='<option value="">Loading products…</option>';});
    if (!category.value) { selectors.forEach(select => select.innerHTML='<option value="">Choose a category first</option>'); return; }
    try {
      const {products} = await post('products', {category_id:category.value});
      selectors.forEach(select => {
        select.innerHTML = '<option value="">Choose a product</option>' + products.map(product => `<option value="${Number(product.id)}">${esc(product.name)} — ${Number(product.price).toFixed(2)} JD</option>`).join('');
        select.disabled = products.length === 0;
        if (!products.length) select.innerHTML='<option value="">No products in this category</option>';
      });
    } catch (error) { status.textContent=error.message; selectors.forEach(select => select.innerHTML='<option value="">Could not load products</option>'); }
  });
  const imageUrl = image => /^https?:\/\//i.test(image) ? image : `/Nexora/${String(image).replaceAll('\\\\','/').replace(/^\/?Nexora\//i,'').replace(/^\//,'')}`;
  const productCell = product => `<div class="compared-product">${product.image ? `<img src="${esc(imageUrl(product.image))}" alt="">` : '<div class="product-placeholder">N</div>'}<strong>${esc(product.name)}</strong></div>`;
  const value = (v, suffix='') => v === null || v === undefined || v === '' ? 'Not provided' : `${esc(v)}${suffix}`;
  button.addEventListener('click', async () => {
    status.textContent=''; button.disabled=true; button.textContent='Analyzing…'; results.hidden=true;
    try {
      const data = await post('compare', {category_id:category.value, first_id:selectors[0].value, second_id:selectors[1].value, purpose:document.getElementById('purpose').value, budget:document.getElementById('budget').value, priority:document.getElementById('priority').value});
      const [a,b] = data.products; const ai=data.analysis;
      document.getElementById('compare-thead').innerHTML=`<tr><th>Product</th><th>${esc(a.name)}</th><th>${esc(b.name)}</th></tr>`;
      const rows=[['Category',a.category_name,b.category_name],['Price',`${Number(a.price).toFixed(2)} JD`,`${Number(b.price).toFixed(2)} JD`],['Stock',`${a.stock_quantity} available`,`${b.stock_quantity} available`],['Customer rating',a.average_rating ? `${a.average_rating} / 5 (${a.review_count} reviews)` : 'No reviews',b.average_rating ? `${b.average_rating} / 5 (${b.review_count} reviews)` : 'No reviews'],['Performance','Not provided','Not provided'],['Memory','Not provided','Not provided'],['Power consumption','Not provided','Not provided'],['Description',a.description || 'Not provided',b.description || 'Not provided']];
      if (Array.isArray(ai.comparison_rows)) ai.comparison_rows.forEach(row=>rows.push([row.label,row.first,row.second]));
      document.getElementById('compare-tbody').innerHTML=rows.map(row=>`<tr><th>${esc(row[0])}</th><td>${row[0]==='Product'?productCell(a):value(row[1])}</td><td>${row[0]==='Product'?productCell(b):value(row[2])}</td></tr>`).join('');
      const winner=document.getElementById('winner-pill');
      const winnerProduct=[a,b].find(product=>Number(product.id)===Number(ai.winner_id));
      winner.hidden=!winnerProduct; winner.textContent=winnerProduct ? `Recommended: ${winnerProduct.name}` : 'No clear winner';
      document.getElementById('ai-summary').textContent=ai.summary || 'AI comparison';
      document.getElementById('winner-reason').textContent=ai.winner_reason || 'The available information is not enough to recommend a winner.';
      document.getElementById('tradeoffs').innerHTML=(Array.isArray(ai.tradeoffs)?ai.tradeoffs:[]).map(item=>`<li>${esc(item)}</li>`).join('');
      results.hidden=false;
    } catch(error) { status.textContent=error.message; }
    finally {button.disabled=false; button.textContent='Compare with AI';}
  });
})();
</script>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
