<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

define('BASE_URL', '/kasir_blue');
define('DB_HOST', 'localhost');
define('DB_NAME', 'kasir_blue');
define('DB_USER', 'root');
define('DB_PASS', '');

define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_PRODUCT', UPLOAD_DIR . '/products');
define('UPLOAD_QRIS', UPLOAD_DIR . '/qris');
if (!is_dir(UPLOAD_PRODUCT)) mkdir(UPLOAD_PRODUCT, 0755, true);
if (!is_dir(UPLOAD_QRIS)) mkdir(UPLOAD_QRIS, 0755, true);

function base_url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
