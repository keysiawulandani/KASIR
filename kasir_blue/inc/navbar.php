<?php
$active = basename($_SERVER['PHP_SELF']);
$user = current_user();
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 8px 20px rgba(15,23,42,0.15);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
            <img src="assets/images/product-default.svg" alt="Logo" style="height:40px; width:40px; border-radius:10px;">
            <span>KANTIN BLUE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($active, ['products.php','product_form.php']) ? 'active' : '' ?>" href="products.php"><i class="fa-solid fa-burger-soda me-2"></i> Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'transactions.php' ? 'active' : '' ?>" href="transactions.php"><i class="fa-solid fa-cart-shopping me-2"></i> Transaksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'stock_history.php' ? 'active' : '' ?>" href="stock_history.php"><i class="fa-solid fa-boxes-stacked me-2"></i> Stok</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'reports.php' ? 'active' : '' ?>" href="reports.php"><i class="fa-solid fa-file-lines me-2"></i> Laporan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'settings.php' ? 'active' : '' ?>" href="settings.php"><i class="fa-solid fa-gear me-2"></i> Pengaturan</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-circle me-2"></i> <?= htmlspecialchars($user['name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="settings.php"><i class="fa-solid fa-cog me-2"></i> Pengaturan</a></li>
                        <li><a class="dropdown-item" href="backup.php"><i class="fa-solid fa-database me-2"></i> Backup</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
