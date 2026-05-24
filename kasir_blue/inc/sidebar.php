<?php
$active = basename($_SERVER['PHP_SELF']);
$user = current_user();
?>
<div class="sidebar">
    <div class="brand">
        <img src="assets/images/product-default.svg" alt="Kantin Blue Logo">
        <div>
            <h4 class="mb-0">KANTIN BLUE</h4>
            <p class="small text-muted mb-0">Kasir Modern</p>
        </div>
    </div>
    <div class="menu">
        <a href="dashboard.php" class="<?= $active === 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
        <a href="products.php" class="<?= in_array($active, ['products.php','product_form.php']) ? 'active' : '' ?>"><i class="fa-solid fa-burger-soda me-2"></i> Produk</a>
        <a href="transactions.php" class="<?= $active === 'transactions.php' ? 'active' : '' ?>"><i class="fa-solid fa-cart-shopping me-2"></i> Transaksi</a>
        <a href="stock_history.php" class="<?= $active === 'stock_history.php' ? 'active' : '' ?>"><i class="fa-solid fa-boxes-stacked me-2"></i> Manajemen Stok</a>
        <a href="reports.php" class="<?= $active === 'reports.php' ? 'active' : '' ?>"><i class="fa-solid fa-file-lines me-2"></i> Laporan</a>
        <a href="settings.php" class="<?= $active === 'settings.php' ? 'active' : '' ?>"><i class="fa-solid fa-gear me-2"></i> Pengaturan</a>
        <a href="backup.php" class="<?= $active === 'backup.php' ? 'active' : '' ?>"><i class="fa-solid fa-database me-2"></i> Backup DB</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
    </div>
    <div class="mt-4 text-muted small">
        Admin: <?= $user ? htmlspecialchars($user['name']) : 'Anonim' ?>
    </div>
</div>
