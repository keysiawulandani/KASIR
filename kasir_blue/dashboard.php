<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$user = current_user();
$page_title = 'Dashboard';
$pdo = pdo();

$total_products = count_rows('products');
$total_transactions = count_rows('transactions');
$total_income = $pdo->query("SELECT COALESCE(SUM(total_bayar),0) FROM transactions WHERE status = 'LUNAS'")->fetchColumn();
$lowStock = $pdo->query('SELECT * FROM products WHERE stok <= minimal_stok ORDER BY stok ASC LIMIT 5')->fetchAll();

$stmt = $pdo->query("SELECT DATE(created_at) AS tanggal, COUNT(*) AS total_transaksi, COALESCE(SUM(total_bayar),0) AS pendapatan FROM transactions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY DATE(created_at)");
$daily = $stmt->fetchAll();
$days = [];
$values = [];
foreach ($daily as $row) {
    $days[] = date('d M', strtotime($row['tanggal']));
    $values[] = $row['pendapatan'];
}

$labels = json_encode($days);
$chartData = json_encode($values);
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar">
        <div>
            <h1 class="h3 mb-1">Selamat datang, <?= htmlspecialchars($user['name']) ?></h1>
            <p class="text-muted">Dashboard kasir modern dengan statistik harian dan notifikasi stok.</p>
        </div>
        <div class="text-end">
            <div class="small text-muted">Tanggal</div>
            <div id="clock" class="h5 fw-semibold"></div>
            <div id="date" class="text-muted"></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card card-glass p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge badge-soft"><i class="fa-solid fa-boxes"></i> Produk</span>
                    <span class="fs-5 fw-semibold">+<?= $total_products ?></span>
                </div>
                <p class="mb-0 text-muted">Total produk tersedia di inventori.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-glass p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge badge-soft"><i class="fa-solid fa-receipt"></i> Transaksi</span>
                    <span class="fs-5 fw-semibold">+<?= $total_transactions ?></span>
                </div>
                <p class="mb-0 text-muted">Jumlah transaksi yang sudah diproses.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-glass p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge badge-soft"><i class="fa-solid fa-wallet"></i> Pendapatan</span>
                    <span class="fs-5 fw-semibold"><?= format_rp($total_income) ?></span>
                </div>
                <p class="mb-0 text-muted">Total pemasukan dari transaksi lunas.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-glass p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge badge-soft"><i class="fa-solid fa-triangle-exclamation"></i> Stok Menipis</span>
                    <span class="fs-5 fw-semibold">+<?= count($lowStock) ?></span>
                </div>
                <p class="mb-0 text-muted">Produk yang butuh restock segera.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card card-glass p-4 shadow-soft">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Statistik Penjualan</h2>
                        <p class="text-muted mb-0">Grafik pendapatan 7 hari terakhir.</p>
                    </div>
                    <span class="badge badge-soft">Realtime insights</span>
                </div>
                <canvas id="salesChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card card-glass p-4 shadow-soft">
                <h2 class="h5 mb-3">Stok Menipis</h2>
                <?php if ($lowStock): ?>
                    <div class="list-group">
                        <?php foreach ($lowStock as $product): ?>
                            <div class="list-group-item list-group-item-action rounded-4 mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($product['nama_produk']) ?></h6>
                                        <small class="text-muted">Stok: <?= $product['stok'] ?> <?= htmlspecialchars($product['unit']) ?></small>
                                    </div>
                                    <span class="badge bg-danger"><?= $product['stok'] <= 0 ? 'Habis' : 'Segera' ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Semua produk stok aman.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-glass p-4 shadow-soft">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Ringkasan Transaksi Terbaru</h2>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Transaksi</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $recent = $pdo->query('SELECT t.kode_transaksi, t.total_item, t.total_bayar, t.status FROM transactions t ORDER BY t.created_at DESC LIMIT 5')->fetchAll();
                        foreach ($recent as $idx => $item):
                        ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= htmlspecialchars($item['kode_transaksi']) ?></td>
                                <td><?= $item['total_item'] ?></td>
                                <td><?= format_rp($item['total_bayar']) ?></td>
                                <td><span class="badge bg-<?= $item['status'] === 'LUNAS' ? 'success' : 'warning' ?>"><?= htmlspecialchars($item['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="toastContainer" class="toast-container"></div>
<?php
$extra_js = "<script>const salesLabels = $labels; const salesValues = $chartData; const ctx = document.getElementById('salesChart').getContext('2d'); new Chart(ctx, { type: 'line', data: { labels: salesLabels, datasets: [{ label: 'Pendapatan', data: salesValues, borderColor: '#2563eb', backgroundColor: 'rgba(59,130,246,0.15)', fill: true, tension: 0.4, pointRadius: 4 }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } } } } } }); function updateClock(){ const now = new Date(); const date = now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric'}); const time = now.toLocaleTimeString('id-ID'); document.getElementById('clock').textContent = time; document.getElementById('date').textContent = date; } setInterval(updateClock, 1000); updateClock();</script>";
?>
<?php require_once __DIR__ . '/inc/footer.php'; ?>