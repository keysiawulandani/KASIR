<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Produk';
$pdo = pdo();
$products = $pdo->query('SELECT p.*, c.name AS kategori, s.name AS supplier FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1">Manajemen Produk</h1>
            <p class="text-muted">Tambah, edit, dan hapus produk dengan tampilan katalog cafe.</p>
        </div>
        <div>
            <a href="product_form.php" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i> Tambah Produk</a>
        </div>
    </div>
    <div class="card card-glass p-4 shadow-soft mb-4">
        <div class="row gy-3">
            <div class="col-md-6">
                <input type="text" id="productSearch" class="form-control form-control-lg" placeholder="Cari produk ...">
            </div>
        </div>
    </div>
    <div class="row g-4" id="productGrid">
        <?php foreach ($products as $product): ?>
            <div class="col-sm-6 col-xl-4 product-card">
                <div class="card product-card shadow-soft">
                    <img src="<?= build_product_image($product['gambar_produk']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nama_produk']) ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge badge-soft"><?= htmlspecialchars($product['kategori']) ?></span>
                            </div>
                            <span class="badge bg-<?= $product['stok'] <= $product['minimal_stok'] ? 'danger' : 'secondary' ?>">Stok <?= $product['stok'] ?></span>
                        </div>
                        <h5 class="card-title mb-2"><?= htmlspecialchars($product['nama_produk']) ?></h5>
                        <p class="card-text text-muted mb-3"><?= htmlspecialchars($product['deskripsi_produk']) ?></p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small">Harga</div>
                                <strong><?= format_rp($product['harga_jual']) ?></strong>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small">Supplier</div>
                                <span><?= htmlspecialchars($product['supplier']) ?></span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="product_form.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-outline-danger btn-sm flex-fill" onclick="return confirm('Hapus produk ini?');"><i class="fa-solid fa-trash"></i> Hapus</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="card card-glass p-4 text-center">
                    <p class="mb-0 text-muted">Belum ada produk. Tambahkan produk baru sekarang.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="toastContainer" class="toast-container"></div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>