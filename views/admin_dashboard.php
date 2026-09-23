<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Pengaduan.php';

$m   = new Pengaduan($conn);
$st  = $m->statistik();
$q   = $m->semua();
$rows = [];
while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;
$rows = array_slice($rows, 0, 6);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Admin — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('dashboard') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Dashboard Admin</h2>
                <p>Pantau dan kelola seluruh pengaduan sarana prasarana sekolah.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <?php if ($st['menunggu'] > 0): ?>
        <div class="noticebox" style="background:linear-gradient(135deg,#fff7ed,#fef3c7);border-color:#fde68a;color:#92400e;">
            Terdapat <strong><?= $st['menunggu'] ?> pengaduan</strong> yang menunggu verifikasi Anda.
            <a href="semua_pengaduan.php?status=Menunggu" style="color:#b45309;font-weight:700;margin-left:8px;">Tangani sekarang</a>
        </div>
        <?php endif; ?>

        <div class="cards">
            <div class="stat-card">
                <div class="stat-icon blue"><?= icon('complaints', '22') ?></div>
                <div>
                    <div class="label">Total Pengaduan</div>
                    <div class="num"><?= $st['total'] ?></div>
                    <div class="sub">semua laporan masuk</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><?= icon('clock', '22') ?></div>
                <div>
                    <div class="label">Menunggu</div>
                    <div class="num"><?= $st['menunggu'] ?></div>
                    <div class="sub">perlu diverifikasi</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><?= icon('tool', '22') ?></div>
                <div>
                    <div class="label">Diproses</div>
                    <div class="num"><?= $st['diproses'] ?></div>
                    <div class="sub">sedang ditangani</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><?= icon('check', '22') ?></div>
                <div>
                    <div class="label">Selesai</div>
                    <div class="num"><?= $st['selesai'] ?></div>
                    <div class="sub">telah diselesaikan</div>
                </div>
            </div>
        </div>

        <div class="panel table-panel mt-28">
            <div class="toolbar">
                <div>
                    <strong class="section-title">Pengaduan Terbaru</strong>
                    <p style="margin:4px 0 0;font-size:12px;color:var(--muted);">6 laporan terbaru masuk</p>
                </div>
                <a href="semua_pengaduan.php" class="btn primary">
                    <span style="display:flex;align-items:center;gap:6px;"><?= icon('list', '14') ?> Lihat Semua</span>
                </a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Nama Siswa</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$rows): ?>
                        <tr><td colspan="7" class="empty-state">Belum ada pengaduan masuk.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($rows as $r): ?>
                        <tr>
                            <td style="color:var(--muted);font-size:12px;font-weight:600;">#<?= str_pad($r['id_pengaduan'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($r['nama']) ?></div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($r['kelas']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($r['nama_kategori']) ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?= htmlspecialchars($r['lokasi']) ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                            <td>
                                <span class="status <?= $r['status']==='Selesai' ? 'selesai' : ($r['status']==='Diproses' ? 'proses' : ($r['status']==='Ditolak' ? 'ditolak' : '')) ?>">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a class="action-btn" href="tanggapan.php?id=<?= $r['id_pengaduan'] ?>">
                                    <span style="display:flex;align-items:center;gap:4px;"><?= icon('eye', '13') ?> <?= $r['status']==='Menunggu' ? 'Tanggapi' : 'Detail' ?></span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
