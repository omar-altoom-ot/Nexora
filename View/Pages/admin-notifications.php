<?php
$currentPage = 'admin-notifications';
$pageTitle = 'Notifications — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;

require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/NotificationController.php';
require_once __DIR__ . '/../Includes/header.php';

$notifController = new NotificationController(new Db());

// Mark all as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all'])) {
    $notifController->markAllAsRead();
    header("Location: ../Layout/routing.php?page=admin-notifications");
    exit;
}

// Pagination logic
$p = max(1, (int)($_GET['p'] ?? 1));
$limit = 10;
$offset = ($p - 1) * $limit;

$notifications = $notifController->getPaginated($limit, $offset);
$total = $notifController->countAll();
$totalPages = max(1, (int)ceil($total / $limit));
?>

<!-- Load orders.css to match the table style -->
<link rel="stylesheet" href="../css/orders.css" />

<?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
<main class="admin-main">
  <header class="admin-top">
    <div>
      <small>ADMIN CONSOLE / <span>NOTIFICATIONS</span></small>
      <h2>Notifications</h2>
    </div>
    <div class="admin-user">
      <span class="live-dot"></span><span>System live</span>
      <a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
    </div>
  </header>
  
  <div class="admin-content">
    <div class="admin-welcome">
      <div>
        <span class="eyebrow">// ACTIVITY FEED</span>
        <h1>System <span class="text-grad">alerts.</span></h1>
        <p class="muted">Stay updated on orders and store activity.</p>
      </div>
      <form method="POST">
        <button type="submit" name="mark_all" class="btn btn-ghost btn-sm">✓ Mark all as read</button>
      </form>
    </div>

    <section class="card panel">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>STATUS</th>
              <th>MESSAGE</th>
              <th>DATE</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($notifications)): ?>
              <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px;">No notifications yet.</td></tr>
            <?php else: foreach ($notifications as $n): 
                $isUnread = (int)$n['is_read'] === 0;
            ?>
              <tr>
                <td>
                  <?php if ($isUnread): ?>
                    <span class="status status-amber">Unread</span>
                  <?php else: ?>
                    <span class="status status-green">Read</span>
                  <?php endif; ?>
                </td>
                <td style="<?= $isUnread ? 'color: #fff; font-weight: bold;' : 'color: var(--muted);' ?>">
                  <?= htmlspecialchars($n['message']) ?>
                </td>
                <td><?= date('M d, Y - h:i A', strtotime($n['created_at'])) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <?php
      $baseUrl = '../Layout/routing.php?page=admin-notifications';
      $prevPage = max(1, $p - 1);
      $nextPage = min($totalPages, $p + 1);
      ?>
      <div class="orders-pagination">
        <a class="page-arrow <?= $p <= 1 ? 'disabled' : '' ?>"
           <?php if ($p > 1): ?> href="<?= $baseUrl ?>&p=<?= $prevPage ?>" <?php endif; ?>
           aria-label="Previous page">←</a>

        <span class="page-info">Page <?= $p ?> / <?= $totalPages ?></span>

        <a class="page-arrow <?= $p >= $totalPages ? 'disabled' : '' ?>"
           <?php if ($p < $totalPages): ?> href="<?= $baseUrl ?>&p=<?= $nextPage ?>" <?php endif; ?>
           aria-label="Next page">→</a>
      </div>
      <?php endif; ?>
    </section>
  </div>
</main>
<?php require_once __DIR__ . '/../Includes/footer.php'; ?>