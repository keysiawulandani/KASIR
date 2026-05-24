<?php
require_once __DIR__ . '/db.php';

function clean($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . base_url($path));
    exit;
}

function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function current_user() {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function auth_check() {
    if (empty($_SESSION['user_id'])) {
        redirect('index.php');
    }
    if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
        session_unset();
        session_destroy();
        session_start();
        set_flash('warning', 'Session Anda telah habis. Silakan login kembali.');
        redirect('index.php');
    }
    $_SESSION['last_activity'] = time();
}

function format_rp($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

function count_rows($table, $where = '1=1', $params = []) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM {$table} WHERE {$where}");
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function get_setting($key) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE `key` = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : null;
}

function save_setting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id FROM settings WHERE `key` = ? LIMIT 1');
    $stmt->execute([$key]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare('UPDATE settings SET value = ? WHERE `key` = ?');
        $stmt->execute([$value, $key]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO settings (`key`, value) VALUES (?, ?)');
        $stmt->execute([$key, $value]);
    }
}

function build_product_image($image) {
    if ($image && file_exists(UPLOAD_PRODUCT . '/' . $image)) {
        return 'uploads/products/' . $image;
    }
    return 'assets/images/product-default.svg';
}
