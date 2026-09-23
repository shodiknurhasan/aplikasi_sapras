<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Kategori.php';

$m     = new Kategori($conn);
$edit  = isset($_GET['id']);
$old   = $edit ? $m->getById($_GET['id']) : null;
$error = '';

if (isset($_POST['simpan'])) {
    $n = trim($_POST['nama_kategori']);
    $d = trim($_POST['deskripsi']);
    if (!$n) {
        $error = 'Nama kategori wajib diisi.';
    } else {
        $ok = $edit ? $m->ubah($_POST['id'], $n, $d) : $m->tambah($n, $d);
        if ($ok) { header('Location: kategori.php'); exit; }
        $error = 'Gagal menyimpan kategori.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $edit ? 'Edit' : 'Tambah' ?> Kategori — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('categories') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>
                <p>Kelola kategori fasilitas yang digunakan pada form pengaduan.</p>
            </div>
        </div>

        <div class="breadcrumb">
            <a href="kategori.php">Kategori Sarana</a>
            <span><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></span>
        </div>

        <div class="panel content-narrow">
            <h3 style="margin:0 0 6px;"><?= $edit ? 'Edit Data Kategori' : 'Kategori Baru' ?></h3>
            <p style="margin:0 0 24px;font-size:13px;color:var(--muted);">
                <?= $edit ? 'Ubah informasi kategori sarana.' : 'Isi nama dan deskripsi untuk kategori fasilitas baru.' ?>
            </p>

            <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?= $old['id_kategori'] ?>">
                <?php endif; ?>

                <div class="field">
                    <label>Nama Kategori <span class="required">*</span></label>
                    <input name="nama_kategori"
                           value="<?= htmlspecialchars($old['nama_kategori'] ?? '') ?>"
                           placeholder="Contoh: Komputer & Elektronik, Meja Kursi, Toilet"
                           required>
                </div>

                <div class="field">
                    <label>Deskripsi <span style="color:var(--muted);font-weight:400;">(opsional)</span></label>
                    <textarea name="deskripsi" placeholder="Jelaskan jenis sarana dalam kategori ini..."><?= htmlspecialchars($old['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a class="btn" href="kategori.php">Batal</a>
                    <button class="btn primary" name="simpan">
                        <span style="display:flex;align-items:center;gap:6px;">
                            <?= icon('save', '14') ?> <?= $edit ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
