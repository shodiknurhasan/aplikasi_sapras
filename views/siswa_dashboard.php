<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireSiswa();
include '../models/Pengaduan.php';

$q    = mysqli_query($conn, "SELECT nis, nama, kelas FROM siswa WHERE username='" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'");
$akun = mysqli_fetch_assoc($q);
$nis  = $akun['nis'];

$m  = new Pengaduan($conn);
$rs = $m->siswa($nis);

$rows = [];
$st   = ['total' => 0, 'Menunggu' => 0, 'Diproses' => 0, 'Selesai' => 0];
while ($r = mysqli_fetch_assoc($rs)) {
    $rows[] = $r;
    $st['total']++;
    if (isset($st[$r['status']])) $st[$r['status']]++;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('dashboard') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Selamat Datang, <?= htmlspecialchars($akun['nama']) ?></h2>
                <p>Pantau dan kelola pengaduan sarana prasarana sekolah Anda.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($akun['nama'], 0, 1)) ?></div>
            </div>
        </div>

        <div class="noticebox">
            Temukan kerusakan fasilitas? Laporkan sekarang dan pantau statusnya secara real-time.
        </div>

        <div class="cards">
            <div class="stat-card">
                <div class="stat-icon blue"><?= icon('list', '22') ?></div>
                <div>
                    <div class="label">Total Pengaduan</div>
                    <div class="num"><?= $st['total'] ?></div>
                    <div class="sub">total laporan dikirim</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><?= icon('clock', '22') ?></div>
                <div>
                    <div class="label">Menunggu</div>
                    <div class="num"><?= $st['Menunggu'] ?></div>
                    <div class="sub">menunggu verifikasi</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><?= icon('tool', '22') ?></div>
                <div>
                    <div class="label">Diproses</div>
                    <div class="num"><?= $st['Diproses'] ?></div>
                    <div class="sub">sedang ditangani</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><?= icon('check', '22') ?></div>
                <div>
                    <div class="label">Selesai</div>
                    <div class="num"><?= $st['Selesai'] ?></div>
                    <div class="sub">pengaduan selesai</div>
                </div>
            </div>
        </div>

        <div class="grid2">
            <div class="panel make-card">
                <div style="width:48px;height:48px;background:var(--primary-bg);border-radius:var(--r);display:flex;align-items:center;justify-content:center;color:var(--primary);margin-bottom:16px;">
                    <?= icon('send', '22') ?>
                </div>
                <h3>Buat Pengaduan Baru</h3>
                <p>Laporkan kerusakan atau masalah fasilitas sekolah agar segera ditangani oleh petugas.</p>
                <div class="push">
                    <a href="form_pengaduan.php" class="btn primary block">
                        <span style="display:flex;align-items:center;gap:7px;justify-content:center;">
                            <?= icon('plus', '15') ?> Buat Pengaduan Sekarang
                        </span>
                    </a>
                    <a href="riwayat.php" class="btn block" style="margin-top:8px;">
                        <span style="display:flex;align-items:center;gap:7px;justify-content:center;">
                            <?= icon('history', '15') ?> Lihat Semua Riwayat
                        </span>
                    </a>
                </div>
            </div>

            <div class="panel table-panel">
                <h3>Pengaduan Terbaru Saya</h3>
                <p>5 pengaduan terakhir yang Anda kirimkan.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$rows): ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    Belum ada pengaduan. <a href="form_pengaduan.php" style="color:var(--primary);">Buat sekarang</a>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php foreach (array_slice($rows, 0, 5) as $r): ?>
                            <tr>
                                <td style="color:var(--muted);font-size:12px;font-weight:600;">#<?= str_pad($r['id_pengaduan'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div style="font-weight:600;color:var(--text);"><?= htmlspecialchars($r['nama_kategori']) ?></div>
                                    <div style="font-size:11px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($r['lokasi']) ?></div>
                                </td>
                                <td style="font-size:12px;color:var(--muted);"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                                <td>
                                    <span class="status <?= $r['status']==='Selesai' ? 'selesai' : ($r['status']==='Diproses' ? 'proses' : ($r['status']==='Ditolak' ? 'ditolak' : '')) ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a class="action-btn" href="detail_pengaduan.php?id=<?= $r['id_pengaduan'] ?>">
                                        <span style="display:flex;align-items:center;gap:4px;"><?= icon('eye', '13') ?> Lihat</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
