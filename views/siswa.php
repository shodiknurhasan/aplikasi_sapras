<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Siswa.php';

$m   = new Siswa($conn);
$msg = '';
$err = false;

if (isset($_GET['hapus'])) {
    $ok  = $m->hapus($_GET['hapus']);
    $msg = $ok ? 'Siswa berhasil dihapus.' : 'Siswa tidak dapat dihapus karena sudah memiliki pengaduan.';
    $err = !$ok;
}
if (isset($_GET['aktif'])) { $m->ubahAktif($_GET['aktif']); header('Location: siswa.php'); exit; }

$q = $m->tampil();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Data Siswa — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('students') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Data Siswa</h2>
                <p>Kelola akun siswa yang dapat mengajukan pengaduan.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="panel table-panel mt-28">
            <div class="toolbar">
                <strong class="section-title">Daftar Siswa</strong>
                <div class="toolbar-actions">
                    <div class="search">
                        <input id="searchInput" placeholder="Cari nama atau NIS..." oninput="filterTable()">
                    </div>
                    <a class="btn primary" href="siswa_form.php">
                        <span style="display:flex;align-items:center;gap:6px;"><?= icon('plus', '13') ?> Tambah Siswa</span>
                    </a>
                </div>
            </div>

            <?php if ($msg): ?>
            <div class="alert <?= $err ? 'error' : 'success' ?>"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <div class="table-wrap">
                <table id="siswaTable">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $empty = true; while ($r = mysqli_fetch_assoc($q)): $empty = false; ?>
                        <tr>
                            <td style="font-family:monospace;font-size:12px;color:var(--muted);"><?= htmlspecialchars($r['nis']) ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;">
                                        <?= strtoupper(substr($r['nama'], 0, 1)) ?>
                                    </div>
                                    <div style="font-weight:600;"><?= htmlspecialchars($r['nama']) ?></div>
                                </div>
                            </td>
                            <td style="font-size:12px;"><?= htmlspecialchars($r['kelas']) ?></td>
                            <td style="font-size:12px;color:var(--muted);">@<?= htmlspecialchars($r['username']) ?></td>
                            <td>
                                <span class="status <?= $r['aktif'] ? 'selesai' : 'ditolak' ?>">
                                    <?= $r['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-row">
                                    <a class="action-btn" href="siswa_form.php?nis=<?= $r['nis'] ?>">
                                        <span style="display:flex;align-items:center;gap:4px;"><?= icon('edit', '13') ?> Edit</span>
                                    </a>
                                    <a class="action-btn danger"
                                       onclick="return confirm('Hapus akun <?= htmlspecialchars(addslashes($r['nama'])) ?>?')"
                                       href="siswa.php?hapus=<?= $r['nis'] ?>">
                                        <span style="display:flex;align-items:center;gap:4px;"><?= icon('trash', '13') ?> Hapus</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($empty): ?>
                        <tr><td colspan="6" class="empty-state">Belum ada data siswa.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#siswaTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>
