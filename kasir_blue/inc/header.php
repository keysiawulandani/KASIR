<?php
if (!defined('APP_HEADER')) {
    define('APP_HEADER', true);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | KANTIN BLUE' : 'KANTIN BLUE' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" crossorigin="anonymous"></script>
</head>
<body>
<?php if (function_exists('flash') && ($flash = flash())): ?>
    <div class="container py-3 mt-2">
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> shadow-sm" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    </div>
<?php endif; ?>
<div class="app-container">
