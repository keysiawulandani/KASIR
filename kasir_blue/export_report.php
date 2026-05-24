<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$pdo = pdo();
$type = $_GET['type'] ?? 'excel';
$start = $_GET['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
$end = $_GET['end_date'] ?? date('Y-m-d');
$where = 'DATE(created_at) BETWEEN :start AND :end';
$params = [':start' => $start, ':end' => $end];
$transactions = $pdo->prepare('SELECT * FROM transactions WHERE ' . $where . ' ORDER BY created_at DESC');
$transactions->execute($params);
$transactions = $transactions->fetchAll();
$topProducts = $pdo->prepare('SELECT p.nama_produk, SUM(td.qty) AS terjual FROM transaction_details td JOIN products p ON td.product_id = p.id JOIN transactions t ON td.transaction_id = t.id WHERE DATE(t.created_at) BETWEEN :start AND :end GROUP BY td.product_id ORDER BY terjual DESC LIMIT 5');
$topProducts->execute($params);
$topProducts = $topProducts->fetchAll();

if ($type === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="laporan_kasir_' . $start . '_sd_' . $end . '.xls"');
} else {
    header('Content-Type: text/html; charset=utf-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan <?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f3f4f6; }
        .header { margin-bottom: 20px; }
        .print-only { display: <?= $type === 'pdf' ? 'block' : 'none' ?>; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Kasir - KANTIN BLUE</h2>
        <p>Periode: <?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?></p>
    </div>
    <h3>Ringkasan Transaksi</h3>
    <table>
        <tr><th>Total Transaksi</th><th>Total Pendapatan</th></tr>
        <tr><td><?= count($transactions) ?></td><td><?= format_rp(array_sum(array_column($transactions, 'total_bayar'))) ?></td></tr>
    </table>
    <h3 class="mt-4">Produk Terlaris</h3>
    <table>
        <thead><tr><th>Produk</th><th>Jumlah Terjual</th></tr></thead>
        <tbody>
            <?php foreach ($topProducts as $product): ?>
            <tr><td><?= htmlspecialchars($product['nama_produk']) ?></td><td><?= $product['terjual'] ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($topProducts)): ?>
            <tr><td colspan="2">Tidak ada data produk terlaris.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <h3 class="mt-4">Detail Transaksi</h3>
    <table>
        <thead>
            <tr><th>No</th><th>Kode</th><th>Pembeli</th><th>Tanggal</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $idx => $trx): ?>
            <tr><td><?= $idx + 1 ?></td><td><?= htmlspecialchars($trx['kode_transaksi']) ?></td><td><?= htmlspecialchars($trx['buyer_name'] ?? 'Umum') ?></td><td><?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></td><td><?= format_rp($trx['total_bayar']) ?></td><td><?= htmlspecialchars($trx['status']) ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr><td colspan="6">Tidak ada transaksi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if ($type === 'pdf'): ?>
    <script>window.addEventListener('load', function(){ window.print(); });</script>
    <?php endif; ?>
</body>
</html>