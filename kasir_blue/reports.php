<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Laporan';
$pdo = pdo();
$start = $_GET['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
$end = $_GET['end_date'] ?? date('Y-m-d');
$where = 'DATE(created_at) BETWEEN :start AND :end';
$params = [':start' => $start, ':end' => $end];

$totalTransaksi = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE $where");
$totalTransaksi->execute($params);
$totalTransaksi = $totalTransaksi->fetchColumn();
$totalPendapatan = $pdo->prepare("SELECT COALESCE(SUM(total_bayar),0) FROM transactions WHERE $where AND status = 'LUNAS'");
$totalPendapatan->execute($params);
$totalPendapatan = $totalPendapatan->fetchColumn();
$produkTerlaris = $pdo->prepare("SELECT p.nama_produk, SUM(td.qty) AS terjual FROM transaction_details td JOIN products p ON td.product_id = p.id JOIN transactions t ON td.transaction_id = t.id WHERE DATE(t.created_at) BETWEEN :start AND :end GROUP BY td.product_id ORDER BY terjual DESC LIMIT 5");
$produkTerlaris->execute($params);
$topProducts = $produkTerlaris->fetchAll();
$transactions = $pdo->prepare('SELECT * FROM transactions WHERE ' . $where . ' ORDER BY created_at DESC');
$transactions->execute($params);
$transactions = $transactions->fetchAll();
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1">Laporan Penjualan</h1>
            <p class="text-muted">Filter tanggal, lihat ringkasan, dan ekspor laporan siap print.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="export_report.php?type=excel&start_date=<?= urlencode($start) ?>&end_date=<?= urlencode($end) ?>" class="btn btn-outline-primary">Export Excel</a>
            <a href="export_report.php?type=pdf&start_date=<?= urlencode($start) ?>&end_date=<?= urlencode($end) ?>" class="btn btn-primary">Export PDF</a>
            <button type="button" class="btn btn-secondary" onclick="window.print();">Print</button>
        </div>
    </div>
    <div class="card card-glass p-4 shadow-soft mb-4">
        <form class="row g-3 align-items-end" method="get">
            <div class="col-sm-5">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start) ?>">
            </div>
            <div class="col-sm-5">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end) ?>">
            </div>
            <div class="col-sm-2">
                <button class="btn btn-primary w-100">Terapkan</button>
            </div>
        </form>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-glass p-4 shadow-soft">
                <h5>Total Transaksi</h5>
                <p class="display-6 mb-0"><?= $totalTransaksi ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-glass p-4 shadow-soft">
                <h5>Total Pendapatan</h5>
                <p class="display-6 mb-0"><?= format_rp($totalPendapatan) ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-glass p-4 shadow-soft">
                <h5>Produk Terlaris</h5>
                <?php foreach ($topProducts as $product): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span><?= htmlspecialchars($product['nama_produk']) ?></span>
                        <strong><?= $product['terjual'] ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($topProducts)): ?>
                    <p class="text-muted mb-0">Data belum tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card card-glass p-4 shadow-soft">
        <h2 class="h5 mb-3">Daftar Transaksi</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $idx => $trx): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><?= htmlspecialchars($trx['kode_transaksi']) ?></td>
                            <td><?= htmlspecialchars($trx['buyer_name'] ?? 'Umum') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></td>
                            <td><?= format_rp($trx['total_bayar']) ?></td>
                            <td><span class="badge bg-<?= $trx['status'] === 'LUNAS' ? 'success' : 'warning' ?>"><?= htmlspecialchars($trx['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada transaksi dalam periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>