<?php
$pageTitle = $pageTitle ?? 'NEXORA';
$pageStylesheet = $pageStylesheet ?? 'pages.css';
$pageLanguage = $pageLanguage ?? 'en';
?>
<!doctype html>
<html lang="<?= htmlspecialchars($pageLanguage, ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&amp;family=Inter:wght@400;500;700&amp;family=Cairo:wght@400;700&amp;display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/theme.css" />
  <link rel="stylesheet" href="../css/<?= htmlspecialchars($pageStylesheet, ENT_QUOTES, 'UTF-8') ?>" />
</head>
<body<?= !empty($isAdminPage) ? ' class="admin-body"' : '' ?>>
