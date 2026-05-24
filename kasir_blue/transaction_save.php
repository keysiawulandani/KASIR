<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$pdo = pdo();
$input = json_decode($_POST['cart_data'] ?? 'null', true);
$buyer_name = trim($_POST['buyer_name'] ?? '');
$buyer_name = $buyer_name !== '' ? clean($buyer_name) : 'Umum';
if (!$input || empty($input['items'])) {
    set_flash('danger', 'Data keranjang tidak valid.');
    redirect('transactions.php');
}
$payment_method = in_array($input['payment_method'] ?? '', ['CASH','QRIS']) ? $input['payment_method'] : 'CASH';
$qris_type = clean($input['qris_type'] ?? '');
$discount = max(0, (int)($input['discount'] ?? 0));
$cash = max(0, (int)($input['cash'] ?? 0));
$items = $input['items'];

$subtotal = 0;
$total_item = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['qty'];
    $total_item += $item['qty'];
}
$tax = round($subtotal * 0.1);
$total = max($subtotal + $tax - $discount, 0);
$status = 'PENDING';
if ($payment_method === 'QRIS' || $cash >= $total) {
    $status = 'LUNAS';
}
if ($payment_method === 'CASH' && $cash < $total) {
    set_flash('danger', 'Uang bayar tidak cukup.');
    redirect('transactions.php');
}

$pdo->beginTransaction();
try {
    $kode = 'TRX' . date('YmdHis') . rand(10,99);
    $stmt = $pdo->prepare('INSERT INTO transactions (kode_transaksi, user_id, buyer_name, total_item, subtotal, pajak, diskon, total_bayar, bayar, kembalian, metode_pembayaran, qris_type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $change = max($cash - $total, 0);
    $stmt->execute([$kode, $_SESSION['user_id'], $buyer_name, $total_item, $subtotal, $tax, $discount, $total, $cash, $change, $payment_method, $qris_type, $status]);
    $transaction_id = $pdo->lastInsertId();
    $detailStmt = $pdo->prepare('INSERT INTO transaction_details (transaction_id, product_id, qty, harga, subtotal) VALUES (?, ?, ?, ?, ?)');
    $updateStockStmt = $pdo->prepare('UPDATE products SET stok = stok - ? WHERE id = ?');
    $historyStmt = $pdo->prepare('INSERT INTO stock_history (product_id, type, quantity, note, created_at) VALUES (?, ?, ?, ?, NOW())');
    foreach ($items as $item) {
        $product = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $product->execute([$item['id']]);
        $row = $product->fetch();
        if (!$row || $row['stok'] < $item['qty']) {
            throw new Exception('Stok tidak cukup untuk ' . htmlspecialchars($item['name']));
        }
        $detailStmt->execute([$transaction_id, $item['id'], $item['qty'], $item['price'], $item['price'] * $item['qty']]);
        $updateStockStmt->execute([$item['qty'], $item['id']]);
        $historyStmt->execute([$item['id'], 'OUT', $item['qty'], 'Penjualan ' . $kode]);
    }
    $paymentStmt = $pdo->prepare('INSERT INTO payments (transaction_id, method, amount, status, created_at) VALUES (?, ?, ?, ?, NOW())');
    $paymentStmt->execute([$transaction_id, $payment_method, $total, $status]);
    $pdo->commit();
    set_flash('success', 'Transaksi berhasil disimpan.');
    redirect('receipt.php?trx=' . urlencode($kode));
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('danger', 'Transaksi gagal: ' . $e->getMessage());
    redirect('transactions.php');
}
