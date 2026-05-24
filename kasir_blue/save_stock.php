<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$pdo = pdo();
$product_id = (int)($_POST['product_id'] ?? 0);
$type = ($_POST['type'] ?? 'IN') === 'OUT' ? 'OUT' : 'IN';
$quantity = max(1, (int)($_POST['quantity'] ?? 0));
$note = clean($_POST['note'] ?? '');

if (!$product_id || $quantity <= 0) {
    set_flash('danger', 'Pilih produk dan masukkan jumlah yang valid.');
    redirect('stock_history.php');
}

$stmt = $pdo->prepare('SELECT stok FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
    set_flash('danger', 'Produk tidak ditemukan.');
    redirect('stock_history.php');
}
$newStock = $type === 'IN' ? $product['stok'] + $quantity : max(0, $product['stok'] - $quantity);
$pdo->beginTransaction();
try {
    $pdo->prepare('UPDATE products SET stok = ? WHERE id = ?')->execute([$newStock, $product_id]);
    $pdo->prepare('INSERT INTO stock_history (product_id, type, quantity, note, created_at) VALUES (?, ?, ?, ?, NOW())')->execute([$product_id, $type, $quantity, $note]);
    $pdo->commit();
    set_flash('success', 'Stok berhasil diperbarui.');
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('danger', 'Gagal memperbarui stok.');
}
redirect('stock_history.php');
