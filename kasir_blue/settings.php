<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Pengaturan Toko';
$storeName = get_setting('store_name') ?: 'KANTIN BLUE';
$storeAddress = get_setting('store_address') ?: 'Jl. Cibadak, Gg. Sereh 26 Bandung';
$logo = get_setting('store_logo');
$qris = get_setting('qris_image');
$logoUrl = $logo ? 'uploads/' . basename($logo) : null;
$qrisUrl = $qris ? 'uploads/qris/' . basename($qris) : null;
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1">Pengaturan Toko</h1>
            <p class="text-muted">Atur nama toko, alamat, logo, dan gambar QRIS pembayaran.</p>
        </div>
    </div>
    <div class="card card-glass p-4 shadow-soft">
        <form action="save_settings.php" method="post" enctype="multipart/form-data" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Toko</label>
                    <input type="text" name="store_name" class="form-control" value="<?= htmlspecialchars($storeName) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat Toko</label>
                    <input type="text" name="store_address" class="form-control" value="<?= htmlspecialchars($storeAddress) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo Toko</label>
                    <input type="file" name="store_logo" class="form-control" accept="image/*">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" class="img-fluid rounded-4 mt-3" style="max-height:120px;">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload QRIS</label>
                    <input type="file" name="qris_image" class="form-control" accept="image/*">
                    <?php if ($qrisUrl): ?>
                        <img src="<?= htmlspecialchars($qrisUrl) ?>" alt="QRIS" class="img-fluid rounded-4 mt-3" style="max-height:120px;">
                    <?php endif; ?>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="toastContainer" class="toast-container"></div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>