<?php
require_once __DIR__ . '/inc/auth.php';
if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = clean($_POST['nisn'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$nisn || !$password) {
        $error = 'NISN/NIK dan password wajib diisi.';
    } else {
        $user = login_user($nisn, $password);
        if ($user) {
            set_flash('success', 'Selamat datang, ' . htmlspecialchars($user['name']) . '!');
            redirect('dashboard.php');
        }
        $error = 'NISN/NIK atau password salah. Coba lagi.';
    }
}
$page_title = 'Login';
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card card-glass p-4 shadow-soft">
                <div class="text-center mb-4">
                    <h1 class="h3">Login Admin</h1>
                    <p class="text-muted">Masuk ke sistem kasir KANTIN BLUE.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger shadow-sm" role="alert"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($flash = flash()): ?>
                    <div class="alert alert-<?= $flash['type'] ?> shadow-sm" role="alert"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">NISN / NIK</label>
                        <input type="text" name="nisn" class="form-control form-control-lg" placeholder="Masukkan NISN/NIK" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Masukkan password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>
                <?php if (!is_admin_registered()): ?>
                <div class="mt-4 text-center">
                    <p class="mb-0">Belum punya akun admin? <a href="register.php">Daftar di sini</a></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>