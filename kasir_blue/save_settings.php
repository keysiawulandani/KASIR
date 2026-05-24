<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$storeName = clean($_POST['store_name'] ?? 'KANTIN BLUE');
$storeAddress = clean($_POST['store_address'] ?? 'Jl. Cibadak, Gg. Sereh 26 Bandung');

if (!empty($_FILES['store_logo']['name'])) {
    $file = $_FILES['store_logo'];
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (in_array($file['type'], $allowed)) {
        $filename = 'logo_' . time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file['name']));
        move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $filename);
        save_setting('store_logo', $filename);
    }
}

if (!empty($_FILES['qris_image']['name'])) {
    $file = $_FILES['qris_image'];
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (in_array($file['type'], $allowed)) {
        $filename = 'qris_' . time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file['name']));
        move_uploaded_file($file['tmp_name'], UPLOAD_QRIS . '/' . $filename);
        save_setting('qris_image', $filename);
    }
}

save_setting('store_name', $storeName);
save_setting('store_address', $storeAddress);
set_flash('success', 'Pengaturan toko berhasil disimpan.');
redirect('settings.php');
