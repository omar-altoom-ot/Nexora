<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../Controllers/CategoryController.php';
if (empty($_SESSION['admin_categories_csrf_token'])) { $_SESSION['admin_categories_csrf_token'] = bin2hex(random_bytes(32)); }
$controller = new CategoryController();
$categories = []; $errors = []; $success = ''; $categoryName = ''; $categoryNameOverrides = []; $modalToOpen = '';

$saveCategoryImage = static function (array $file): ?string {
  if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
  if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 5 * 1024 * 1024) {
    throw new InvalidArgumentException('Choose an image up to 5 MB.');
  }
  $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
  $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
  if (!isset($extensions[$mime]) || @getimagesize($file['tmp_name']) === false) {
    throw new InvalidArgumentException('Choose a valid JPG, PNG, WebP, or GIF image.');
  }
  $directory = __DIR__ . '/../../Assets/Images/categories';
  if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
    throw new RuntimeException('Unable to save the category image.');
  }
  $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
  if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
    throw new RuntimeException('Unable to save the category image.');
  }
  return 'Assets/Images/categories/' . $filename;
};

$categoryImageUrl = static function (?string $image): string {
  $image = trim((string) $image);
  if ($image === '') return '';
  if (preg_match('~\Ahttps?://~i', $image)) return $image;

  $image = str_replace('\\', '/', $image);
  $image = preg_replace('~\A/?Nexora/~i', '', $image);
  return '/Nexora/' . ltrim($image, '/');
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf_token'] ?? '';
  if (!is_string($token) || !hash_equals($_SESSION['admin_categories_csrf_token'], $token)) { http_response_code(403); exit('Invalid CSRF token.'); }
  $action = $_POST['action'] ?? '';
  $postedId = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
  $modalToOpen = $action === 'create' ? 'create-category-modal' : ($action === 'update' && $postedId ? 'edit-category-' . (int) $postedId : '');
  try {
    if ($action === 'create') {
      $categoryName = is_string($_POST['name'] ?? null) ? $_POST['name'] : '';
      $image = $saveCategoryImage($_FILES['image'] ?? []);
      $controller->createCategory($categoryName, $image);
      header('Location: ../Layout/routing.php?page=admin-categories&notice=created'); exit;
    }
    $id = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id) throw new InvalidArgumentException('Choose a valid category.');
    if ($action === 'update') {
      $name = is_string($_POST['name'] ?? null) ? $_POST['name'] : '';
      $categoryNameOverrides[(int) $id] = $name;
      $image = $saveCategoryImage($_FILES['image'] ?? []);
      $controller->updateCategory($id, $name, $image);
      header('Location: ../Layout/routing.php?page=admin-categories&notice=updated'); exit;
    }
    if ($action === 'delete') {
      $controller->deleteCategory($id);
      header('Location: ../Layout/routing.php?page=admin-categories&notice=deleted'); exit;
    }
    throw new InvalidArgumentException('Invalid category action.');
  } catch (InvalidArgumentException $e) { $errors[] = $e->getMessage(); }
  catch (PDOException $e) {
    error_log($e->getMessage());
    $errors[] = $e->getCode() === '23000'
      ? 'This category still has products. Move or remove its products before deleting it.'
      : 'That category name already exists.';
  } catch (RuntimeException $e) { $errors[] = $e->getMessage(); }
}
if (isset($_GET['notice'])) { $success = match ($_GET['notice']) { 'created' => 'Category created successfully.', 'updated' => 'Category updated successfully.', 'deleted' => 'Category deleted successfully.', default => '' }; }
try { $categories = $controller->getAll(); }
catch (PDOException $e) { error_log($e->getMessage()); $errors[] = 'Unable to load categories. Please try again later.'; }
$currentPage = 'admin-categories'; $pageTitle = 'Categories — NEXORA Admin'; $pageStylesheet = 'pages.css'; $isAdminPage = true; $showStoreFooter = false;
require_once __DIR__ . '/../Includes/header.php';
?>
<link rel="stylesheet" href="../css/orders.css">
<style>
.category-admin-grid{align-items:stretch;position:relative;overflow:visible}.category-admin-card{position:relative;z-index:0;display:flex;flex-direction:column;align-items:stretch;gap:0;padding:0;overflow:visible;border:1px solid var(--line);background:linear-gradient(145deg,rgba(24,19,48,.96),rgba(10,8,24,.98));transition:transform .2s,border-color .2s,box-shadow .2s}.category-admin-card:hover{transform:translateY(-3px);border-color:rgba(34,211,238,.38);box-shadow:0 12px 34px rgba(0,0,0,.28)}.category-admin-card.menu-open{z-index:30;transform:translateY(-3px)}.category-card-image{height:170px;position:relative;overflow:hidden;border-radius:inherit;border-bottom-left-radius:0;border-bottom-right-radius:0;background:radial-gradient(circle at 50% 45%,rgba(139,92,246,.32),transparent 66%),var(--panel-2);display:grid;place-items:center}.category-card-image img{width:100%;height:100%;object-fit:cover;display:block}.category-image-placeholder{font-size:3.5rem;color:var(--cyan);text-shadow:0 0 28px rgba(34,211,238,.3)}.category-card-copy{padding:17px 18px 18px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.category-card-name{font:700 1rem var(--font-head);margin:1px 0 8px;overflow-wrap:anywhere}.category-product-count{display:inline-flex;align-items:center;gap:7px;color:var(--muted);font-size:.7rem;letter-spacing:.08em}.category-product-count b{color:var(--cyan);font:700 1rem var(--font-head)}.category-menu{position:relative;flex:none;z-index:2}.category-menu>summary{list-style:none;cursor:pointer;width:42px;height:42px;display:grid;place-items:center;border:1px solid rgba(34,211,238,.65);border-radius:13px;color:#fff;background:rgba(34,211,238,.07);font-size:1.05rem;font-weight:800;letter-spacing:2px;line-height:1;transition:transform .18s,border-color .18s,background .18s,box-shadow .18s}.category-menu>summary::-webkit-details-marker{display:none}.category-menu>summary:hover,.category-menu[open]>summary{transform:translateY(-1px);border-color:var(--cyan);background:rgba(34,211,238,.16);box-shadow:0 0 18px rgba(34,211,238,.16);color:#fff}.category-menu-panel{position:absolute;z-index:50;inset-inline-end:0;top:calc(100% + 9px);min-width:170px;padding:7px;border:1px solid rgba(139,92,246,.34);border-radius:13px;background:linear-gradient(145deg,#17112e,#0c0919);box-shadow:0 18px 42px rgba(0,0,0,.62);display:grid;gap:4px}.category-menu-panel button{width:100%;min-height:40px;padding:10px 12px;border:1px solid transparent;border-radius:9px;display:flex;align-items:center;gap:10px;text-align:left;background:transparent;color:var(--text);font:600 .82rem var(--font-body);cursor:pointer;transition:background .16s,border-color .16s,transform .16s}.category-menu-panel button:hover{transform:translateX(2px);background:rgba(139,92,246,.16);border-color:rgba(139,92,246,.25)}.category-menu-panel .menu-option-icon{width:23px;height:23px;display:grid;place-items:center;border-radius:7px;background:rgba(34,211,238,.12);color:var(--cyan);font-size:.75rem}.category-menu-panel form{margin:0}.category-menu-panel form+form{border-top:1px solid rgba(139,92,246,.2);padding-top:4px}.category-menu-panel .delete-action{color:#fb7185}.category-menu-panel .delete-action .menu-option-icon{background:rgba(251,113,133,.12);color:#fb7185}.category-menu-panel .delete-action:hover{background:rgba(251,113,133,.1);border-color:rgba(251,113,133,.2)}.category-edit-panel{padding:0 18px 18px;border-top:1px solid rgba(139,92,246,.14)}.category-edit-panel[hidden]{display:none}.category-edit-panel textarea{width:100%;min-height:76px;resize:vertical;margin:14px 0 10px;font:600 .95rem var(--font-head)}.category-image-picker{height:100px;position:relative;overflow:hidden;border:1px dashed rgba(34,211,238,.4);border-radius:10px;background:rgba(139,92,246,.07);display:grid;place-items:center;cursor:pointer;color:var(--muted);font-size:.75rem}.category-image-picker img{width:100%;height:100%;object-fit:cover}.category-image-picker span{position:absolute;inset:auto 8px 8px;padding:6px 8px;text-align:center;border-radius:6px;background:rgba(5,4,13,.78);color:#fff}.category-image-picker input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}.category-edit-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:12px}.category-edit-actions .btn{padding:8px 12px}.category-add-image{display:block;margin:0 0 15px}.category-empty{padding:28px;text-align:center;color:var(--muted)}.category-empty h3{color:var(--text)}.category-notice{margin:0 0 16px}.category-card-count-label{display:block;margin-top:3px;color:var(--muted);font-size:.62rem;letter-spacing:.06em}
 .category-modal-content{width:min(520px,calc(100vw - 32px));max-height:calc(100vh - 36px);overflow:auto}.category-modal-content .field{margin:16px 0}.category-modal-content textarea{min-height:76px;resize:vertical}.category-modal-content .category-image-picker{height:145px}
</style>
<?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
<main class="admin-main">
  <header class="admin-top"><div><small>ADMIN CONSOLE / <span>CATEGORIES</span></small><h2>Categories</h2></div><div class="admin-user"><span class="live-dot"></span><span>System live</span><a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a></div></header>
  <div class="admin-content">
    <div class="admin-welcome"><div><span class="eyebrow">// ORGANIZE THE ARENA</span><h1>Product <span class="text-grad">categories.</span></h1><p class="muted">Keep your catalog clear and easy to explore.</p></div><button class="btn btn-primary btn-sm" type="button" data-category-create>＋ New category</button></div>
    <div class="grid grid-3 category-admin-grid">
      <?php if (!$categories): ?><article class="card category-empty"><h3>No categories yet</h3><p>Create a category to organize your products.</p></article>
      <?php else: foreach ($categories as $category): $categoryId = (int) $category['id']; $imagePath = $categoryImageUrl($category['image'] ?? null); ?>
        <article class="card category-admin category-admin-card">
          <div class="category-card-image" data-image-wrap>
            <?php if ($imagePath !== ''): ?><img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?> category image" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><span class="category-image-placeholder" hidden>◈</span>
            <?php else: ?><span class="category-image-placeholder">◈</span><?php endif; ?>
          </div>
          <div class="category-card-copy"><div><h3 class="category-card-name"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></h3><span class="category-product-count"><b><?= (int) $category['product_count'] ?></b> PRODUCTS</span></div>
            <details class="category-menu"><summary aria-label="Category actions">•••</summary><div class="category-menu-panel">
              <button type="button" data-edit-toggle="<?= $categoryId ?>"><span class="menu-option-icon">✎</span><span>Edit</span></button>
              <form method="post" action="../Layout/routing.php?page=admin-categories"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_categories_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="category_id" value="<?= $categoryId ?>"><button type="submit" class="delete-action"><span class="menu-option-icon">⌫</span><span>Delete</span></button></form>
            </div></details>
          </div>
        </article>
      <?php endforeach; endif; ?>
    </div>
  </div>
</main>
<div class="modal-overlay category-modal-overlay" id="create-category-modal" aria-hidden="true"><div class="modal-content category-modal-content" role="dialog" aria-modal="true" aria-labelledby="create-category-title">
  <h3 id="create-category-title">Create category</h3><form method="post" enctype="multipart/form-data" action="../Layout/routing.php?page=admin-categories">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_categories_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="create">
    <label class="field"><span>Category name</span><textarea class="input" name="name" maxlength="100" required><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></textarea></label>
    <label class="category-image-picker"><input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-preview><span>＋ Choose category image (optional)</span><img alt="Image preview" hidden></label>
    <div class="modal-actions" style="margin-top:20px"><button class="btn btn-ghost btn-sm" type="button" data-modal-close>Cancel</button><button class="btn btn-primary btn-sm" type="submit">Create category</button></div>
  </form>
</div></div>
<?php foreach ($categories as $category): $categoryId = (int) $category['id']; $imagePath = $categoryImageUrl($category['image'] ?? null); ?>
<div class="modal-overlay category-modal-overlay" id="edit-category-<?= $categoryId ?>" aria-hidden="true"><div class="modal-content category-modal-content" role="dialog" aria-modal="true" aria-labelledby="edit-category-title-<?= $categoryId ?>">
  <h3 id="edit-category-title-<?= $categoryId ?>">Edit category</h3><form method="post" enctype="multipart/form-data" action="../Layout/routing.php?page=admin-categories">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_categories_csrf_token'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="category_id" value="<?= $categoryId ?>">
    <label class="field"><span>Category name</span><textarea class="input" name="name" maxlength="100" required><?= htmlspecialchars($categoryNameOverrides[$categoryId] ?? $category['name'], ENT_QUOTES, 'UTF-8') ?></textarea></label>
    <label class="category-image-picker"><input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-preview><span>Click to change image</span><?php if ($imagePath !== ''): ?><img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>" alt="Current category image" onerror="this.hidden=true"><?php else: ?><img alt="Image preview" hidden><?php endif; ?></label>
    <div class="modal-actions" style="margin-top:20px"><button class="btn btn-ghost btn-sm" type="button" data-modal-close>Cancel</button><button class="btn btn-primary btn-sm" type="submit">Save changes</button></div>
  </form>
</div></div>
<?php endforeach; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showCategoryModal(id){var modal=document.getElementById(id);if(!modal)return;modal.style.display='flex';modal.setAttribute('aria-hidden','false');var field=modal.querySelector('textarea');if(field)field.focus();}
function closeCategoryModal(modal){modal.style.display='none';modal.setAttribute('aria-hidden','true');}
document.querySelector('[data-category-create]').addEventListener('click',function(){showCategoryModal('create-category-modal');});
document.querySelectorAll('[data-edit-toggle]').forEach(function(button){button.addEventListener('click',function(){showCategoryModal('edit-category-'+button.dataset.editToggle);button.closest('details').open=false;});});
document.querySelectorAll('[data-modal-close]').forEach(function(button){button.addEventListener('click',function(){closeCategoryModal(button.closest('.category-modal-overlay'));});});
document.querySelectorAll('.category-modal-overlay').forEach(function(modal){modal.addEventListener('click',function(event){if(event.target===modal)closeCategoryModal(modal);});});
document.querySelectorAll('[data-image-preview]').forEach(function (input) {
  input.addEventListener('change', function () {
    var file = input.files && input.files[0]; if (!file) return;
    var img = input.closest('label').querySelector('img');
    if (!img) { img = document.createElement('img'); input.closest('label').appendChild(img); }
    img.src = URL.createObjectURL(file); img.hidden = false;
    var hint = input.closest('label').querySelector('span'); if (hint) hint.textContent = file.name;
  });
});
document.querySelectorAll('.category-menu').forEach(function (menu) {
  menu.addEventListener('toggle', function () {
    var card = menu.closest('.category-admin-card');
    card.classList.toggle('menu-open', menu.open);
    if (menu.open) document.querySelectorAll('.category-menu[open]').forEach(function (other) { if (other !== menu) other.open = false; });
  });
});document.addEventListener('click', function (event) {
  document.querySelectorAll('.category-menu[open]').forEach(function (menu) { if (!menu.contains(event.target)) menu.open = false; });
});
document.querySelectorAll('.category-menu-panel form').forEach(function(form){form.addEventListener('submit',function(event){event.preventDefault();Swal.fire({title:'Delete category?',text:'This action removes the category from the database.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',cancelButtonText:'Cancel',reverseButtons:true,buttonsStyling:false,customClass:{popup:'nexora-deactivate-modal',confirmButton:'nexora-confirm-danger',cancelButton:'nexora-cancel-neutral'},background:'#15102a',color:'#f9f5ff'}).then(function(result){if(result.isConfirmed)form.submit();});});});
<?php if ($modalToOpen !== ''): ?>showCategoryModal(<?= json_encode($modalToOpen) ?>);<?php endif; ?>
<?php if ($success !== ''): ?>Swal.fire({toast:true,position:'top-end',icon:'success',title:<?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:3000,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});
<?php elseif ($errors): ?>Swal.fire({toast:true,position:'top-end',icon:'error',title:<?= json_encode(implode(' ', $errors), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,showConfirmButton:false,timer:4500,timerProgressBar:true,background:'#1d152d',color:'#f9f5ff'});<?php endif; ?>
</script>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>
