<?php
// includes/head.php
// Usage: set $pageTitle before including if needed
if (!isset($pageTitle)) $pageTitle = 'Website System Database';

// Tentukan root project (segmen pertama pada SCRIPT_NAME) sehingga asset dapat diakses
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$segments = explode('/', trim($script, '/'));
$root = (isset($segments[0]) && $segments[0] !== '') ? '/' . $segments[0] : '';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $u489872923_rab_system ?>/assets/style.css">
</head>
<body class="bg-light">
