<?php
require_once __DIR__ . '/inc/auth.php';
if (is_admin_registered()) {
    redirect('index.php');
}
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = clean($_POST['nisn'] ?? '');
    $name = clean($_POST['name'] ?? 'Admin');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$nisn || !$password || !$confirm) {
        $error = 'Semua kolom wajib diisi.';
    } elseif ($password !== $confirm) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = pdo()->prepare('INSERT INTO users (nisn, password, name, role, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$nisn, $hashed, $name, 'admin']);
        set_flash('success', 'Registrasi admin berhasil. Silakan login.');
        redirect('index.php');
    }
}
$page_title = 'Register Admin';
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-glass p-4 shadow-soft">
                <div class="text-center mb-4">
                    <h1 class="h3">Register Admin</h1>
                    <p class="text-muted">Buat akun administrator untuk mengelola kasir.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger shadow-sm" role="alert"><?= $error ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">NISN / NIK</label>
                        <input type="text" name="nisn" class="form-control form-control-lg" placeholder="Masukkan NISN/NIK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Admin</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Konfirmasi password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Daftar Admin</button>
                    </div>
                </form>
                <div class="mt-4 text-center">
                    <p class="mb-0">Sudah punya akun? <a href="index.php">Login sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>