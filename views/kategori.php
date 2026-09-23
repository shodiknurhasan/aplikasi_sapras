<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Kategori.php';

$m     = new Kategori($conn);
$msg   = '';
$gagal = false;

if (isset($_GET['hapus'])) {
    $ok    = $m->hapus($_GET['hapus']);
    $msg   = $ok ? 'Kategori berhasil dihapus.' : 'Kategori tidak dapat dihapus karena sudah dipakai pengaduan.';
    $gagal = !$ok;
}
if (isset($_GET['aktif'])) { $m->ubahAktif($_GET['aktif']); header('Location: kategori.php'); exit; }

$q = $m->tampil();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kategori Sarana — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('categories') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Kategori Sarana</h2>
                <p>Kelola kategori fasilitas yang tersedia pada form pengaduan.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="panel table-panel mt-28">
            <div class="toolbar">
                <strong class="section-title">Daftar Kategori</strong>
                <a class="btn primary" href="kategori_form.php">
                    <span style="display:flex;align-items:center;gap:6px;"><?= icon('plus', '13') ?> Tambah Kategori</span>
                </a>
            </div>

            <?php if ($msg): ?>
            <div class="alert <?= $gagal ? 'error' : 'success' ?>"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; $empty = true; while ($r = mysqli_fetch_assoc($q)): $empty = false; ?>
                        <tr>
                            <td style="color:var(--muted);font-size:12px;"><?= $no++ ?></td>
                            <td><div style="font-weight:600;"><?= htmlspecialchars($r['nama_kategori']) ?></div></td>
                            <td style="font-size:12px;color:var(--muted);"><?= $r['deskripsi'] ? htmlspecialchars($r['deskripsi']) : '<em>—</em>' ?></td>
                            <td>
                                <span class="status <?= $r['aktif'] ? 'selesai' : 'ditolak' ?>">
                                    <?= $r['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-row">
                                    <a class="action-btn" href="kategori_form.php?id=<?= $r['id_kategori'] ?>">
                                        <span style="display:flex;align-items:center;gap:4px;"><?= icon('edit', '13') ?> Edit</span>
                                    </a>
                                    <a class="action-btn danger"
                                       onclick="return confirm('Hapus kategori ini? Kategori yang sudah dipakai tidak dapat dihapus.')"
                                       href="kategori.php?hapus=<?= $r['id_kategori'] ?>">
                                        <span style="display:flex;align-items:center;gap:4px;"><?= icon('trash', '13') ?> Hapus</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($empty): ?>
                        <tr><td colspan="5" class="empty-state">Belum ada kategori.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
