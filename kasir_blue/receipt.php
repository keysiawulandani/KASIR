<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$pdo = pdo();
$kode = $_GET['trx'] ?? '';
if (!$kode) {
    redirect('dashboard.php');
}
$stmt = $pdo->prepare('SELECT t.*, u.name AS kasir FROM transactions t LEFT JOIN users u ON t.user_id = u.id WHERE t.kode_transaksi = ? LIMIT 1');
$stmt->execute([$kode]);
$transaction = $stmt->fetch();
if (!$transaction) {
    set_flash('danger', 'Struk transaksi tidak ditemukan.');
    redirect('dashboard.php');
}
$details = $pdo->prepare('SELECT td.*, p.nama_produk FROM transaction_details td LEFT JOIN products p ON td.product_id = p.id WHERE td.transaction_id = ?');
$details->execute([$transaction['id']]);
$details = $details->fetchAll();
$page_title = 'Struk ' . $kode;
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<div class="container py-5">
    <div class="card p-4 card-glass shadow-soft" style="max-width:720px; margin:auto;">
        <div class="text-center mb-4">
            <h2 class="mb-1">KANTIN BLUE</h2>
            <p class="text-muted mb-1">Jl. Cibadak, Gg. Sereh 26 Bandung</p>
            <p class="small text-muted">Struk Pembelian Cafe / Kantin</p>
        </div>
        <div class="mb-3 row text-muted">
            <div class="col-6"><strong>No Transaksi:</strong> <?= htmlspecialchars($transaction['kode_transaksi']) ?></div>
            <div class="col-6 text-end"><strong>Tgl:</strong> <?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></div>
            <div class="col-6"><strong>Kasir:</strong> <?= htmlspecialchars($transaction['kasir']) ?></div>
            <div class="col-6 text-end"><strong>Metode:</strong> <?= htmlspecialchars($transaction['metode_pembayaran']) ?></div>
            <div class="col-6"><strong>Pembeli:</strong> <?= htmlspecialchars($transaction['buyer_name'] ?? 'Umum') ?></div>
        </div>
        <div class="table-responsive mb-3">
            <table class="table table-borderless">
                <thead class="text-muted small">
                    <tr>
                        <th>Menu</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $detail): ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['nama_produk']) ?></td>
                            <td class="text-end"><?= $detail['qty'] ?></td>
                            <td class="text-end"><?= format_rp($detail['harga']) ?></td>
                            <td class="text-end"><?= format_rp($detail['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between"><span>Subtotal</span><strong><?= format_rp($transaction['subtotal']) ?></strong></div>
            <div class="d-flex justify-content-between"><span>Pajak (10%)</span><strong><?= format_rp($transaction['pajak']) ?></strong></div>
            <div class="d-flex justify-content-between"><span>Diskon</span><strong><?= format_rp($transaction['diskon']) ?></strong></div>
            <hr>
            <div class="d-flex justify-content-between"><span>Total Bayar</span><strong><?= format_rp($transaction['total_bayar']) ?></strong></div>
            <div class="d-flex justify-content-between"><span>Uang Bayar</span><strong><?= format_rp($transaction['bayar']) ?></strong></div>
            <div class="d-flex justify-content-between"><span>Kembalian</span><strong><?= format_rp($transaction['kembalian']) ?></strong></div>
        </div>
        <div class="text-center text-muted small mb-3">Terima kasih telah berbelanja</div>
        <div class="d-flex justify-content-between">
            <a href="transactions.php" class="btn btn-secondary">Kembali</a>
            <button type="button" class="btn btn-primary" onclick="window.print();"><i class="fa-solid fa-print me-2"></i> Cetak Struk</button>
        </div>
    </div>
</div>
<style>
    @media print {
        body { background: #fff !important; }
        .card { box-shadow: none !important; }
        .btn { display: none !important; }
        .alert { display: none !important; }
    }
</style>
<?php require_once __DIR__ . '/inc/footer.php'; ?>