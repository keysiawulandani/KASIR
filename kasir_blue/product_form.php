<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Form Produk';
$pdo = pdo();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$suppliers = $pdo->query('SELECT * FROM suppliers ORDER BY name')->fetchAll();
$product = null;
if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
}
$action = $product ? 'Edit Produk' : 'Tambah Produk';
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1"><?= $action ?></h1>
            <p class="text-muted">Kelola data produk dengan form yang lengkap.</p>
        </div>
    </div>
    <div class="card card-glass p-4 shadow-soft">
        <form action="save_product.php" method="post" enctype="multipart/form-data" novalidate>
            <?php if ($product): ?>
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <?php endif; ?>
            <div class="row gy-3">
                <div class="col-md-6">
                    <label class="form-label">Kode Produk</label>
                    <input type="text" name="kode_produk" class="form-control" value="<?= htmlspecialchars($product['kode_produk'] ?? 'KNT' . rand(100,999)) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($product['nama_produk'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Produk</label>
                    <textarea name="deskripsi_produk" class="form-control" rows="3" required><?= htmlspecialchars($product['deskripsi_produk'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= isset($product['category_id']) && $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Pilih Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>" <?= isset($product['supplier_id']) && $product['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>><?= htmlspecialchars($supplier['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit</label>
                    <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($product['unit'] ?? 'pcs') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($product['stok'] ?? 0) ?>" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minimal Stok</label>
                    <input type="number" name="minimal_stok" class="form-control" value="<?= htmlspecialchars($product['minimal_stok'] ?? 5) ?>" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Beli</label>
                    <input type="number" name="harga_beli" class="form-control" value="<?= htmlspecialchars($product['harga_beli'] ?? 0) ?>" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control" value="<?= htmlspecialchars($product['harga_jual'] ?? 0) ?>" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Ditambahkan</label>
                    <input type="date" name="tanggal_ditambahkan" class="form-control" value="<?= htmlspecialchars($product['tanggal_ditambahkan'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Produk</label>
                    <input type="file" name="gambar_produk" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <?php if (!empty($product['gambar_produk'])): ?>
                        <label class="form-label">Preview Gambar Saat Ini</label>
                        <img src="<?= build_product_image($product['gambar_produk']) ?>" alt="Preview" class="img-fluid rounded-4 d-block">
                    <?php endif; ?>
                </div>
                <div class="col-12 text-end">
                    <a href="products.php" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>