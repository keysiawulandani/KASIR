<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Backup Database';
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1">Backup Database</h1>
            <p class="text-muted">Unduh skrip SQL untuk backup dan restore database kasir.</p>
        </div>
    </div>
    <div class="card card-glass p-4 shadow-soft">
        <h2 class="h5 mb-3">Download Backup</h2>
        <p class="text-muted">File SQL berisi struktur tabel, relasi, dan data dummy produk.</p>
        <a href="sql/kantin_blue.sql" class="btn btn-primary"><i class="fa-solid fa-download me-2"></i> Unduh SQL Backup</a>
    </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>