<?php
session_start();
include 'config/koneksi.php';
include 'models/User.php';

if (isset($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'views/admin_dashboard.php' : 'views/siswa_dashboard.php'));
    exit;
}

$error = '';
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';

if (isset($_POST['login'])) {
    $u = new User($conn);
    $data = $u->login($_POST['username'] ?? '', $_POST['password'] ?? '');

    if ($data) {
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama']     = $data['nama'];
        $_SESSION['role']     = $data['role'];

        if ($data['role'] === 'siswa') {
            $qk = mysqli_query($conn, "SELECT kelas FROM siswa WHERE username='" . mysqli_real_escape_string($conn, $data['username']) . "'");
            $rk = mysqli_fetch_assoc($qk);
            $_SESSION['kelas'] = $rk['kelas'] ?? '';
        }

        header('Location: ' . ($data['role'] === 'admin' ? 'views/admin_dashboard.php' : 'views/siswa_dashboard.php'));
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body class="login-page">

<div class="login-card">
    <div class="login-brand">
        <div class="login-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20"/><path d="M9 22V12h6v10"/><path d="M8 7h1"/><path d="M15 7h1"/><path d="M8 11h1"/><path d="M15 11h1"/></svg>
        </div>
        <div>
            <h1>SIGAP SARPRAS</h1>
            <p>Sistem Pengaduan Sarana Prasarana</p>
        </div>
    </div>

    <div class="login-heading">
        <h2>Selamat Datang</h2>
        <p>Masuk untuk mengakses sistem pengaduan sarana prasarana sekolah.</p>
    </div>

    <?php if ($registered): ?>
    <div class="alert success login-alert">
        Registrasi berhasil. Silakan login dengan akun baru Anda.
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert error login-alert">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form class="login-form" method="post">
        <div class="field">
            <label>Username</label>
            <input name="username" placeholder="Masukkan username Anda" required autocomplete="username">
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password Anda" required autocomplete="current-password">
        </div>
        <button class="login-btn" name="login" type="submit">Masuk ke Sistem</button>
    </form>

    <div class="login-foot">
        Belum punya akun? <a href="register.php">Daftar sebagai siswa</a>
    </div>
</div>

</body>
</html>
