<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Siswa.php';

$m     = new Siswa($conn);
$edit  = isset($_GET['nis']);
$old   = $edit ? $m->getByNis($_GET['nis']) : null;
$error = '';

if (isset($_POST['simpan'])) {
    $nis_lama = $_POST['nis_lama'] ?? '';
    $nis  = trim($_POST['nis']);
    $n    = trim($_POST['nama']);
    $k    = trim($_POST['kelas']);
    $u    = trim($_POST['username']);
    $p    = $_POST['password'] ?? '';

    if (!$nis || !$n || !$k || !$u || (!$edit && !$p)) {
        $error = 'Semua data wajib diisi.';
    } else {
        $ok = $edit ? $m->ubah($nis_lama, $nis, $n, $k, $u, $p) : $m->tambah($nis, $n, $k, $u, $p);
        if ($ok) { header('Location: siswa.php'); exit; }
        $error = 'Gagal menyimpan. Username atau NIS mungkin sudah digunakan.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $edit ? 'Edit' : 'Tambah' ?> Siswa — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('students') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2><?= $edit ? 'Edit Siswa' : 'Tambah Siswa' ?></h2>
                <p>Kelola data akun siswa yang dapat mengakses sistem.</p>
            </div>
        </div>

        <div class="breadcrumb">
            <a href="siswa.php">Data Siswa</a>
            <span><?= $edit ? 'Edit Data' : 'Tambah Siswa' ?></span>
        </div>

        <div class="panel content-narrow">
            <h3 style="margin:0 0 6px;"><?= $edit ? 'Edit Data Siswa' : 'Siswa Baru' ?></h3>
            <p style="margin:0 0 24px;font-size:13px;color:var(--muted);">
                <?= $edit ? 'Ubah data akun siswa di bawah ini.' : 'Isi data siswa untuk membuat akun baru.' ?>
            </p>

            <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <?php if ($edit): ?>
                <input type="hidden" name="nis_lama" value="<?= htmlspecialchars($old['nis']) ?>">
                <?php endif; ?>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="field">
                        <label>NIS <span class="required">*</span></label>
                        <input type="number" name="nis" value="<?= htmlspecialchars($old['nis'] ?? '') ?>" placeholder="Nomor Induk Siswa" required>
                    </div>
                    <div class="field">
                        <label>Kelas <span class="required">*</span></label>
                        <input name="kelas" value="<?= htmlspecialchars($old['kelas'] ?? '') ?>" placeholder="Contoh: XII RPL 4" required>
                    </div>
                </div>

                <div class="field">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input name="nama" value="<?= htmlspecialchars($old['nama'] ?? '') ?>" placeholder="Nama lengkap siswa" required>
                </div>

                <div class="field">
                    <label>Username <span class="required">*</span></label>
                    <input name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" placeholder="Username untuk login" required>
                </div>

                <div class="field">
                    <label>
                        Password
                        <?php if ($edit): ?>
                        <span style="color:var(--muted);font-weight:400;">(kosongkan jika tidak diubah)</span>
                        <?php else: ?>
                        <span class="required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="password" name="password"
                           placeholder="<?= $edit ? 'Kosongkan jika tidak ingin mengubah password' : 'Buat password' ?>"
                           <?= $edit ? '' : 'required' ?>>
                </div>

                <div class="form-actions">
                    <a class="btn" href="siswa.php">Batal</a>
                    <button class="btn primary" name="simpan">
                        <span style="display:flex;align-items:center;gap:6px;">
                            <?= icon('save', '14') ?> <?= $edit ? 'Simpan Perubahan' : 'Tambah Siswa' ?>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
