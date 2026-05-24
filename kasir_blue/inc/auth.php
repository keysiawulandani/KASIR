<?php
require_once __DIR__ . '/functions.php';

function ensure_login() {
    auth_check();
}

function is_admin_registered() {
    global $pdo;
    $stmt = $pdo->query('SELECT COUNT(*) FROM users');
    return $stmt->fetchColumn() > 0;
}

function login_user($nisn, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE nisn = ? LIMIT 1');
    $stmt->execute([$nisn]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time();
        return $user;
    }
    return false;
}
