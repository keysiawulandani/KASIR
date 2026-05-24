<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$pdo = pdo();
$id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = $pdo->prepare('SELECT gambar_produk FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if ($product && !empty($product['gambar_produk']) && file_exists(UPLOAD_PRODUCT . '/' . $product['gambar_produk'])) {
        @unlink(UPLOAD_PRODUCT . '/' . $product['gambar_produk']);
    }
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    set_flash('success', 'Produk berhasil dihapus.');
}
redirect('products.php');
