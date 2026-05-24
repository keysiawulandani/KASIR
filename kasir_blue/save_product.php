<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$pdo = pdo();

$id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$kode_produk = clean($_POST['kode_produk'] ?? '');
$nama_produk = clean($_POST['nama_produk'] ?? '');
$deskripsi_produk = clean($_POST['deskripsi_produk'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$supplier_id = (int)($_POST['supplier_id'] ?? 0);
$unit = clean($_POST['unit'] ?? 'pcs');
$stok = max(0, (int)($_POST['stok'] ?? 0));
$minimal_stok = max(0, (int)($_POST['minimal_stok'] ?? 0));
$harga_beli = max(0, (int)($_POST['harga_beli'] ?? 0));
$harga_jual = max(0, (int)($_POST['harga_jual'] ?? 0));
$tanggal_ditambahkan = $_POST['tanggal_ditambahkan'] ?? date('Y-m-d');

if (!$kode_produk || !$nama_produk || !$deskripsi_produk || !$category_id || !$supplier_id) {
    set_flash('danger', 'Lengkapi semua data produk.');
    redirect('product_form.php' . ($id ? '?id=' . $id : ''));
}

$imageName = null;
if (!empty($_FILES['gambar_produk']['name'])) {
    $file = $_FILES['gambar_produk'];
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array($file['type'], $allowed)) {
        set_flash('danger', 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.');
        redirect('product_form.php' . ($id ? '?id=' . $id : ''));
    }
    $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file['name']));
    move_uploaded_file($file['tmp_name'], UPLOAD_PRODUCT . '/' . $imageName);
}

if ($id) {
    $stmt = $pdo->prepare('SELECT gambar_produk FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($imageName && !empty($existing['gambar_produk']) && file_exists(UPLOAD_PRODUCT . '/' .$existing['gambar_produk'])) {
        @unlink(UPLOAD_PRODUCT . '/' . $existing['gambar_produk']);
    }
    if (!$imageName) {
        $imageName = $existing['gambar_produk'];
    }
    $stmt = $pdo->prepare('UPDATE products SET kode_produk = ?, nama_produk = ?, deskripsi_produk = ?, category_id = ?, supplier_id = ?, stok = ?, minimal_stok = ?, unit = ?, harga_beli = ?, harga_jual = ?, gambar_produk = ?, tanggal_ditambahkan = ? WHERE id = ?');
    $stmt->execute([$kode_produk, $nama_produk, $deskripsi_produk, $category_id, $supplier_id, $stok, $minimal_stok, $unit, $harga_beli, $harga_jual, $imageName, $tanggal_ditambahkan, $id]);
    set_flash('success', 'Produk berhasil diperbarui.');
} else {
    $stmt = $pdo->prepare('INSERT INTO products (kode_produk, nama_produk, deskripsi_produk, category_id, supplier_id, stok, minimal_stok, unit, harga_beli, harga_jual, gambar_produk, tanggal_ditambahkan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$kode_produk, $nama_produk, $deskripsi_produk, $category_id, $supplier_id, $stok, $minimal_stok, $unit, $harga_beli, $harga_jual, $imageName, $tanggal_ditambahkan]);
    set_flash('success', 'Produk baru berhasil ditambahkan.');
}
redirect('products.php');
