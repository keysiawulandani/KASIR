<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Manajemen Stok';
$pdo = pdo();
$products = $pdo->query('SELECT id, nama_produk, stok, unit FROM products ORDER BY nama_produk')->fetchAll();
$history = $pdo->query('SELECT h.*, p.nama_produk FROM stock_history h LEFT JOIN products p ON h.product_id = p.id ORDER BY h.created_at DESC LIMIT 20')->fetchAll();
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1">Manajemen Stok</h1>
            <p class="text-muted">Tambah, kurangi, dan lihat riwayat stok masuk/keluar.</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card card-glass p-4 shadow-soft">
                <h2 class="h5 mb-3">Tambah / Kurangi Stok</h2>
                <form action="save_stock.php" method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['nama_produk']) ?> - Stok <?= $product['stok'] ?> <?= htmlspecialchars($product['unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select" required>
                            <option value="IN">Masuk</option>
                            <option value="OUT">Keluar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="note" class="form-control" placeholder="Contoh: Restock seminggu, retur, dll.">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan Riwayat</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card card-glass p-4 shadow-soft">
                <h2 class="h5 mb-3">Riwayat Stok Terakhir</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Produk</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $item): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                    <td><span class="badge bg-<?= $item['type'] === 'IN' ? 'success' : 'danger' ?>"><?= $item['type'] === 'IN' ? 'Masuk' : 'Keluar' ?></span></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= htmlspecialchars($item['note']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($history)): ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada riwayat stok.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="toastContainer" class="toast-container"></div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>