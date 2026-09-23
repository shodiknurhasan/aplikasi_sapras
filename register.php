<?php
session_start();
include 'config/koneksi.php';
include 'models/User.php';

if (isset($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'views/admin_dashboard.php' : 'views/siswa_dashboard.php'));
    exit;
}

$error = '';

if (isset($_POST['register'])) {
    $nis        = trim($_POST['nis'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');
    $kelas      = trim($_POST['kelas'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if ($nis === '' || $nama === '' || $kelas === '' || $username === '' || $password === '' || $konfirmasi === '') {
        $error = 'Semua data wajib diisi.';
    } elseif (!ctype_digit($nis)) {
        $error = 'NIS harus berupa angka.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $u      = new User($conn);
        $result = $u->register($nis, $nama, $kelas, $username, $password);
        if ($result['ok']) {
            header('Location: index.php?registered=1');
            exit;
        }
        $error = $result['message'];
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registrasi — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
    <style>
        .register-card {
            width: min(520px, calc(100% - 32px));
            background: rgba(255,255,255,.97);
            border-radius: var(--r-xl);
            padding: 44px 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 1;
        }
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .register-foot {
            margin-top: 22px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }
        .register-foot a { color: var(--primary); font-weight: 600; }
        @media (max-width: 480px) {
            .register-card { padding: 32px 22px; }
            .field-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="login-page">

<div class="register-card">
    <div class="login-brand">
        <div class="login-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20"/><path d="M9 22V12h6v10"/><path d="M8 7h1"/><path d="M15 7h1"/><path d="M8 11h1"/><path d="M15 11h1"/></svg>
        </div>
        <div>
            <h1>SIGAP SARPRAS</h1>
            <p>Buat Akun Siswa Baru</p>
        </div>
    </div>

    <div class="login-heading">
        <h2>Daftar Akun</h2>
        <p>Lengkapi data berikut untuk membuat akun siswa.</p>
    </div>

    <?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="field-row">
            <div class="field">
                <label>NIS <span class="required">*</span></label>
                <input type="number" name="nis"
                       value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>"
                       placeholder="Contoh: 22230502" required>
            </div>
            <div class="field">
                <label>Kelas <span class="required">*</span></label>
                <input name="kelas"
                       value="<?= htmlspecialchars($_POST['kelas'] ?? '') ?>"
                       placeholder="Contoh: XII RPL 4" required>
            </div>
        </div>

        <div class="field">
            <label>Nama Lengkap <span class="required">*</span></label>
            <input name="nama"
                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                   placeholder="Masukkan nama lengkap sesuai data sekolah" required>
        </div>

        <div class="field">
            <label>Username <span class="required">*</span></label>
            <input name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   placeholder="Buat username untuk login" required>
        </div>

        <div class="field-row">
            <div class="field">
                <label>Password <span class="required">*</span></label>
                <input type="password" name="password" placeholder="Min. 6 karakter" required>
            </div>
            <div class="field">
                <label>Konfirmasi Password <span class="required">*</span></label>
                <input type="password" name="konfirmasi_password" placeholder="Ulangi password" required>
            </div>
        </div>

        <button class="login-btn" name="register" type="submit" style="margin-top:6px;">
            Buat Akun Sekarang
        </button>
    </form>

    <div class="register-foot">
        Sudah punya akun? <a href="index.php">Kembali ke Login</a>
    </div>
</div>

</body>
</html>
