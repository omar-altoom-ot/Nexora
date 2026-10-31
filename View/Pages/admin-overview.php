<?php
$exportBufferLevel = ob_get_level();
ob_start();
$currentPage = 'admin-overview';
$pageTitle = 'Overview — NEXORA Admin';
$pageStylesheet = 'pages.css';
$isAdminPage = true;
$showStoreFooter = false;
require_once __DIR__ . '/../../Controllers/CategoryController.php';
require_once __DIR__ . '/../../Config/DBconnection.php';
require_once __DIR__ . '/../../Controllers/orders.php';
$categories = [];
$latestOrders = [];
$dashboard = [
  'gross_revenue' => 0.0,
  'orders' => 0,
  'active_customers' => 0,
  'low_stock' => 0,
];
$dailyOrders = [];
$dailyCustomers = [];
$orderStatusCounts = [];
$revenueRange = ($_GET['revenue_range'] ?? '30') === '7' ? 7 : 30;
$today = new DateTimeImmutable('today');
$historyStart = $today->modify('-59 days');
$currentPeriodStart = $today->modify('-29 days');
$trendValues = ['revenue' => [0.0, 0.0], 'orders' => [0, 0], 'customers' => [0, 0]];
$chartMonthLabels = [];
$chartMax = 4.0;
$chartTickLabels = ['0', '0', '0', '0', '0'];
$orderStatusTotal = 0;
try {
  $categories = (new CategoryController())->getAll();
} catch (PDOException $e) {
  error_log($e->getMessage());
}
try {
  $db = new Db();
  $orderController = new OrderController($db);
  $latestOrders = $orderController->getOrdersFiltered('', '', 3, 0);

  $dashboard['gross_revenue'] = (float) $db->run_query(
    "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status <> 'cancelled'"
  )->fetchColumn();
  $dashboard['orders'] = (int) $db->run_query('SELECT COUNT(*) FROM orders')->fetchColumn();
  $dashboard['active_customers'] = (int) $db->run_query(
    'SELECT COUNT(*) FROM customers WHERE is_active = 1'
  )->fetchColumn();
  $dashboard['low_stock'] = (int) $db->run_query(
    'SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity <= 5'
  )->fetchColumn();

  $orderRows = $db->run_query(
    'SELECT DATE(order_date) AS day,
            SUM(CASE WHEN status <> \'cancelled\' THEN total_amount ELSE 0 END) AS revenue,
            COUNT(*) AS order_count
     FROM orders WHERE order_date >= :start_date
     GROUP BY DATE(order_date)',
    ['start_date' => $historyStart->format('Y-m-d')]
  )->fetchAll();
  foreach ($orderRows as $row) {
    $day = (string) $row['day'];
    $dailyOrders[$day] = [
      'revenue' => (float) $row['revenue'],
      'orders' => (int) $row['order_count'],
    ];
    $date = new DateTimeImmutable($day);
    $periodIndex = $date >= $currentPeriodStart ? 0 : 1;
    $trendValues['revenue'][$periodIndex] += (float) $row['revenue'];
    $trendValues['orders'][$periodIndex] += (int) $row['order_count'];
  }

  $customerRows = $db->run_query(
    'SELECT DATE(created_at) AS day, COUNT(*) AS customer_count
     FROM customers WHERE is_active = 1 AND created_at >= :start_date
     GROUP BY DATE(created_at)',
    ['start_date' => $historyStart->format('Y-m-d')]
  )->fetchAll();
  foreach ($customerRows as $row) {
    $day = (string) $row['day'];
    $dailyCustomers[$day] = (int) $row['customer_count'];
    $date = new DateTimeImmutable($day);
    $periodIndex = $date >= $currentPeriodStart ? 0 : 1;
    $trendValues['customers'][$periodIndex] += (int) $row['customer_count'];
  }

  foreach ($db->run_query('SELECT status, COUNT(*) AS total FROM orders GROUP BY status')->fetchAll() as $row) {
    $orderStatusCounts[(string) $row['status']] = (int) $row['total'];
    $orderStatusTotal += (int) $row['total'];
  }
} catch (PDOException $e) {
  error_log($e->getMessage());
}

$chartStart = $today->modify('-' . ($revenueRange - 1) . ' days');
$revenueValues = [];
for ($day = $chartStart; $day <= $today; $day = $day->modify('+1 day')) {
  $key = $day->format('Y-m-d');
  $revenueValues[] = ['date' => $day, 'value' => (float) ($dailyOrders[$key]['revenue'] ?? 0)];
}
$maxRevenue = max(0.0, ...array_column($revenueValues, 'value'));
$chartMax = max(4.0, ceil($maxRevenue / 4) * 4);
$chartCoordinates = [];
$lastIndex = max(1, count($revenueValues) - 1);
foreach ($revenueValues as $index => $point) {
  $x = ($index / $lastIndex) * 700;
  $y = 195 - (175 * ($point['value'] / $chartMax));
  $chartCoordinates[] = number_format($x, 1, '.', '') . ' ' . number_format($y, 1, '.', '');
}
$chartLinePath = 'M' . implode(' L', $chartCoordinates);
$chartAreaPath = $chartLinePath . ' L700 220 L0 220 Z';
$chartTickLabels = array_map(static function (int $tick) use ($chartMax): string {
  $value = $chartMax * (4 - $tick) / 4;
  return $value >= 1000 ? rtrim(rtrim(number_format($value / 1000, 1), '0'), '.') . 'k' : number_format($value, 0);
}, [0, 1, 2, 3, 4]);
foreach ([0, 1, 2, 3, 4] as $labelIndex) {
  $pointIndex = (int) round($labelIndex * $lastIndex / 4);
  $chartMonthLabels[] = $revenueValues[$pointIndex]['date']->format('M d');
}
$trend = static function (float $current, float $previous): array {
  if ($previous <= 0) {
    return ['—', ''];
  }
  $change = (($current - $previous) / $previous) * 100;
  return [($change >= 0 ? '↗ ' : '↘ ') . number_format(abs($change), 1) . '%', $change < 0 ? 'warn' : ''];
};
[$revenueTrend, $revenueTrendClass] = $trend(...$trendValues['revenue']);
[$ordersTrend, $ordersTrendClass] = $trend(...$trendValues['orders']);
[$customersTrend, $customersTrendClass] = $trend(...$trendValues['customers']);
$sparkline = static function (array $values): string {
  $max = max(1, ...$values);
  $bars = '';
  foreach ($values as $value) {
    $height = max(3, (int) round(((float) $value / $max) * 20));
    $bars .= '<i style="display:block;flex:1;height:' . $height . 'px;background:currentColor;border-radius:1px 1px 0 0"></i>';
  }
  return $bars;
};
$recentDays = [];
for ($day = $today->modify('-11 days'); $day <= $today; $day = $day->modify('+1 day')) {
  $recentDays[] = $day->format('Y-m-d');
}
$revenueSpark = array_map(static fn(string $day): float => (float) ($dailyOrders[$day]['revenue'] ?? 0), $recentDays);
$ordersSpark = array_map(static fn(string $day): int => (int) ($dailyOrders[$day]['orders'] ?? 0), $recentDays);
$customersSpark = array_map(static fn(string $day): int => (int) ($dailyCustomers[$day] ?? 0), $recentDays);
$statusDelivered = (int) ($orderStatusCounts['delivered'] ?? 0);
$statusProcessing = (int) ($orderStatusCounts['pending'] ?? 0) + (int) ($orderStatusCounts['confirmed'] ?? 0) + (int) ($orderStatusCounts['shipped'] ?? 0);
$statusCancelled = (int) ($orderStatusCounts['cancelled'] ?? 0);
$statusDeliveredEnd = $orderStatusTotal > 0 ? ($statusDelivered / $orderStatusTotal) * 100 : 0;
$statusProcessingEnd = $orderStatusTotal > 0 ? (($statusDelivered + $statusProcessing) / $orderStatusTotal) * 100 : 0;
$statusDeliveredPercent = $orderStatusTotal > 0 ? ($statusDelivered / $orderStatusTotal) * 100 : 0;
$statusProcessingPercent = $orderStatusTotal > 0 ? ($statusProcessing / $orderStatusTotal) * 100 : 0;
$statusCancelledPercent = $orderStatusTotal > 0 ? ($statusCancelled / $orderStatusTotal) * 100 : 0;
$statusDonut = $orderStatusTotal > 0
  ? 'conic-gradient(var(--green) 0% ' . number_format($statusDeliveredEnd, 2, '.', '') . '%, var(--cyan) ' . number_format($statusDeliveredEnd, 2, '.', '') . '% ' . number_format($statusProcessingEnd, 2, '.', '') . '%, var(--pink) ' . number_format($statusProcessingEnd, 2, '.', '') . '% 100%)'
  : 'conic-gradient(rgba(139,92,246,.25) 0% 100%)';

if (($_GET['export'] ?? '') === 'report') {
  $csvSafe = static function ($value): string {
    $value = (string) $value;
    return preg_match('/^[=+@\-]/u', $value) ? "'" . $value : $value;
  };
  while (ob_get_level() > $exportBufferLevel) {
    ob_end_clean();
  }
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="nexora-admin-report-' . date('Y-m-d') . '.csv"');
  header('Cache-Control: no-store, no-cache, must-revalidate');
  $csv = fopen('php://output', 'wb');
  fwrite($csv, "\xEF\xBB\xBF");
  fputcsv($csv, ['NEXORA Admin Overview Report']);
  fputcsv($csv, ['Generated at', date('Y-m-d H:i:s')]);
  fputcsv($csv, ['Revenue period', 'Last ' . $revenueRange . ' days']);
  fputcsv($csv, []);
  fputcsv($csv, ['Summary']);
  fputcsv($csv, ['Metric', 'Value']);
  fputcsv($csv, ['Gross revenue (JD)', number_format($dashboard['gross_revenue'], 2, '.', '')]);
  fputcsv($csv, ['Orders', $dashboard['orders']]);
  fputcsv($csv, ['Active customers', $dashboard['active_customers']]);
  fputcsv($csv, ['Low stock alerts', $dashboard['low_stock']]);
  fputcsv($csv, []);
  fputcsv($csv, ['Revenue by day']);
  fputcsv($csv, ['Date', 'Revenue (JD)']);
  foreach ($revenueValues as $point) {
    fputcsv($csv, [$point['date']->format('Y-m-d'), number_format($point['value'], 2, '.', '')]);
  }
  fputcsv($csv, []);
  fputcsv($csv, ['Order status', 'Count', 'Share']);
  foreach (['Delivered' => $statusDeliveredPercent, 'Processing' => $statusProcessingPercent, 'Cancelled' => $statusCancelledPercent] as $label => $share) {
    $count = match ($label) {
      'Delivered' => $statusDelivered,
      'Processing' => $statusProcessing,
      default => $statusCancelled,
    };
    fputcsv($csv, [$label, $count, number_format($share, 1) . '%']);
  }
  fputcsv($csv, []);
  fputcsv($csv, ['Latest orders']);
  fputcsv($csv, ['Order ID', 'Customer', 'Date', 'Amount (JD)', 'Status']);
  foreach ($latestOrders as $order) {
    $customer = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));
    fputcsv($csv, [
      '#NX-' . (int) $order->id,
      $csvSafe($customer),
      date('Y-m-d', strtotime($order->order_date)),
      number_format((float) $order->total_amount, 2, '.', ''),
      $csvSafe($order->status),
    ]);
  }
  fputcsv($csv, []);
  fputcsv($csv, ['Categories']);
  fputcsv($csv, ['Category', 'Product count']);
  foreach ($categories as $category) {
    fputcsv($csv, [$csvSafe($category['name']), (int) ($category['product_count'] ?? 0)]);
  }
  fclose($csv);
  exit;
}
require_once __DIR__ . '/../Includes/header.php';
?>
    <?php require __DIR__ . '/../Layout/admin_nav.php'; ?>
    <main class="admin-main">
      <header class="admin-top">
        <div>
          <small>ADMIN CONSOLE / <span>OVERVIEW</span></small>
          <h2>Overview</h2>
        </div>
        <div class="admin-user">
          <span class="live-dot"></span><span>System live</span
          ><a class="admin-avatar" href="../Layout/routing.php?page=admin-profile">AD</a>
        </div>
      </header>
      <div class="admin-content">
        <div class="admin-welcome">
          <div>
            <span class="eyebrow">// COMMAND OVERVIEW</span>
            <h1>Good morning, <span class="text-grad">Alex.</span></h1>
            <p class="muted">Here’s what’s happening across NEXORA today.</p>
          </div>
          <a class="btn btn-ghost btn-sm" href="../Layout/routing.php?page=admin-overview&amp;export=report&amp;revenue_range=<?= $revenueRange ?>">↓ Export report</a>
        </div>
        <div class="grid grid-4 kpi-grid">
          <div class="kpi card">
            <small>GROSS REVENUE <span class="<?= $revenueTrendClass ?>"><?= htmlspecialchars($revenueTrend, ENT_QUOTES, 'UTF-8') ?></span></small>
            <b>JD <?= number_format($dashboard['gross_revenue'], 2) ?></b>
            <div class="sparkline" style="display:flex;align-items:end;gap:4px;height:20px"><?= $sparkline($revenueSpark) ?></div>
          </div>
          <div class="kpi card">
            <small>ORDERS <span class="<?= $ordersTrendClass ?>"><?= htmlspecialchars($ordersTrend, ENT_QUOTES, 'UTF-8') ?></span></small>
            <b><?= number_format($dashboard['orders']) ?></b>
            <div class="sparkline cyan" style="display:flex;align-items:end;gap:4px;height:20px"><?= $sparkline($ordersSpark) ?></div>
          </div>
          <div class="kpi card">
            <small>ACTIVE CUSTOMERS <span class="<?= $customersTrendClass ?>"><?= htmlspecialchars($customersTrend, ENT_QUOTES, 'UTF-8') ?></span></small>
            <b><?= number_format($dashboard['active_customers']) ?></b>
            <div class="sparkline blue" style="display:flex;align-items:end;gap:4px;height:20px"><?= $sparkline($customersSpark) ?></div>
          </div>
          <div class="kpi card">
            <small>LOW STOCK ALERTS <span class="warn">!</span></small
            ><b><?= str_pad((string) $dashboard['low_stock'], 2, '0', STR_PAD_LEFT) ?></b>
            <div class="kpi-note">Requires attention</div>
          </div>
        </div>
        <div class="admin-panels">
          <section class="card panel chart-panel">
            <div class="row-between">
              <div>
                <h3>Revenue performance</h3>
                <p class="muted">Net sales · last <?= $revenueRange ?> days</p>
              </div>
              <form method="get" action="../Layout/routing.php">
                <input type="hidden" name="page" value="admin-overview">
                <select class="input" name="revenue_range" onchange="this.form.submit()">
                  <option value="30" <?= $revenueRange === 30 ? 'selected' : '' ?>>Last 30 days</option>
                  <option value="7" <?= $revenueRange === 7 ? 'selected' : '' ?>>Last 7 days</option>
                </select>
              </form>
            </div>
            <div class="chart">
              <div class="chart-grid">
                <?php foreach ($chartTickLabels as $tickLabel): ?><span><?= htmlspecialchars($tickLabel, ENT_QUOTES, 'UTF-8') ?></span><?php endforeach; ?>
              </div>
              <svg
                viewBox="0 0 700 220"
                preserveAspectRatio="none"
                aria-label="Revenue trending upward"
              >
                <defs>
                  <linearGradient id="area" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0" stop-color="#8b5cf6" stop-opacity=".38" />
                    <stop offset="1" stop-color="#8b5cf6" stop-opacity="0" />
                  </linearGradient>
                </defs>
                <path
                  d="<?= htmlspecialchars($chartAreaPath, ENT_QUOTES, 'UTF-8') ?>"
                  fill="url(#area)"
                />
                <path
                  d="<?= htmlspecialchars($chartLinePath, ENT_QUOTES, 'UTF-8') ?>"
                  fill="none"
                  stroke="#a78bfa"
                  stroke-width="3"
                  vector-effect="non-scaling-stroke"
                />
              </svg>
            </div>
            <div class="chart-months">
              <?php foreach ($chartMonthLabels as $label): ?><span><?= htmlspecialchars(strtoupper($label), ENT_QUOTES, 'UTF-8') ?></span><?php endforeach; ?>
            </div>
          </section>
          <section class="card panel">
            <div class="row-between">
              <div>
                <h3>Order status</h3>
                <p class="muted"><?= number_format($orderStatusTotal) ?> total orders</p>
              </div>
              <a href="../Layout/routing.php?page=admin-orders">View all →</a>
            </div>
            <div class="donut-wrap">
              <div class="donut" style="background:<?= htmlspecialchars($statusDonut, ENT_QUOTES, 'UTF-8') ?>">
                <span><?= number_format($orderStatusTotal) ?><small>ORDERS</small></span>
              </div>
              <div class="legend">
                <span><i class="green-dot"></i>Delivered <b><?= number_format($statusDeliveredPercent, 1) ?>%</b></span>
                <span><i class="blue-dot"></i>Processing <b><?= number_format($statusProcessingPercent, 1) ?>%</b></span>
                <span><i class="pink-dot"></i>Cancelled <b><?= number_format($statusCancelledPercent, 1) ?>%</b></span>
              </div>
            </div>
          </section>
        </div>
        <section class="card panel">
          <div class="row-between">
            <div>
              <h3>Latest orders</h3>
              <p class="muted">Recent customer activity</p>
            </div>
            <a href="../Layout/routing.php?page=admin-orders">All orders →</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ORDER ID</th>
                  <th>CUSTOMER</th>
                  <th>DATE</th>
                  <th>AMOUNT</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($latestOrders): ?>
                  <?php foreach ($latestOrders as $order): ?>
                    <?php
                      $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));
                      $statusClass = match ($order->status) {
                        'delivered' => 'status-green',
                        'confirmed' => 'status-blue',
                        'shipped' => 'status-pink',
                        'cancelled' => 'status-red',
                        default => 'status-amber',
                      };
                    ?>
                    <tr>
                      <td>#NX-<?= (int) $order->id ?></td>
                      <td><?= htmlspecialchars($customerName !== '' ? $customerName : 'Customer', ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars(date('M d, Y', strtotime($order->order_date)), ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= number_format((float) $order->total_amount, 2) ?> JD</td>
                      <td><span class="status <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($order->status), ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:24px;">No orders found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
        <section class="card panel">
          <div class="row-between">
            <div>
              <h3>Categories</h3>
              <p class="muted">Categories from your product catalog</p>
            </div>
            <a href="../Layout/routing.php?page=admin-categories">Manage categories →</a>
          </div>
          <div class="grid grid-4 category-admin-grid">
            <?php if ($categories): ?>
              <?php foreach ($categories as $category): ?>
                <?php
                  $categoryImage = trim((string) ($category['image'] ?? ''));
                  $categoryName = (string) $category['name'];
                ?>
                <a class="card category-admin" href="../Layout/routing.php?page=admin-categories">
                  <div class="category-icon">
                    <?php if ($categoryImage !== ''): ?>
                      <img src="/Nexora/<?= htmlspecialchars(ltrim($categoryImage, '/'), ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                    <?php else: ?>◈<?php endif; ?>
                  </div>
                  <div>
                    <h3><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= (int) ($category['product_count'] ?? 0) ?> products</p>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="muted">No categories are available right now.</p>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </main>
  <?php require_once __DIR__ . '/../Includes/footer.php'; ?>

