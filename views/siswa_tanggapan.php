<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireSiswa();

$q    = mysqli_query($conn, "SELECT nis, nama FROM siswa WHERE username='" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'");
$akun = mysqli_fetch_assoc($q);
$nis  = (int)$akun['nis'];

$sql = "SELECT pn.status, pn.feedback, pn.catatan, pn.created_at, p.id_pengaduan, k.nama_kategori, p.lokasi
        FROM penanganan pn
        JOIN pengaduan p ON p.id_pengaduan = pn.id_pengaduan
        JOIN kategori k  ON k.id_kategori  = p.id_kategori
        WHERE p.nis = $nis
        ORDER BY pn.created_at DESC";
$rs = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tanggapan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('tanggapan') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Tanggapan Petugas</h2>
                <p>Balasan dan update status dari petugas atas pengaduan Anda.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($akun['nama'], 0, 1)) ?></div>
            </div>
        </div>

        <div class="panel mt-28">
            <?php $ada = false; while ($r = mysqli_fetch_assoc($rs)): $ada = true; ?>
            <div class="feedback-item">
                <div class="feedback-head">
                    <div>
                        <div style="font-size:11px;color:var(--primary);font-weight:600;margin-bottom:4px;">
                            Laporan #<?= str_pad($r['id_pengaduan'], 4, '0', STR_PAD_LEFT) ?>
                        </div>
                        <strong><?= htmlspecialchars($r['nama_kategori']) ?> — <?= htmlspecialchars($r['lokasi']) ?></strong>
                    </div>
                    <span class="status <?= $r['status']==='Selesai' ? 'selesai' : ($r['status']==='Diproses' ? 'proses' : ($r['status']==='Ditolak' ? 'ditolak' : '')) ?>">
                        <?= htmlspecialchars($r['status']) ?>
                    </span>
                </div>
                <div class="feedback-date"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></div>

                <?php if ($r['feedback']): ?>
                <div class="feedback-text">
                    <div style="font-size:11px;font-weight:600;color:var(--muted);margin-bottom:4px;">Pesan dari Petugas</div>
                    <?= nl2br(htmlspecialchars($r['feedback'])) ?>
                </div>
                <?php else: ?>
                <div class="feedback-text" style="color:var(--muted);font-style:italic;">Tidak ada pesan tambahan dari petugas.</div>
                <?php endif; ?>

                <div class="feedback-action">
                    <a class="action-btn" href="detail_pengaduan.php?id=<?= $r['id_pengaduan'] ?>">
                        <span style="display:flex;align-items:center;gap:4px;"><?= icon('eye', '13') ?> Lihat Detail</span>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>

            <?php if (!$ada): ?>
            <div style="text-align:center;padding:50px 20px;color:var(--muted);">
                <div style="margin-bottom:14px;opacity:.4;"><?= icon('message', '48') ?></div>
                <div style="font-size:16px;font-weight:600;color:var(--text-2);margin-bottom:8px;">Belum ada tanggapan</div>
                <div style="font-size:13px;">Petugas akan memberikan tanggapan setelah memverifikasi pengaduan Anda.</div>
                <a href="form_pengaduan.php" class="btn primary" style="margin-top:20px;">Buat Pengaduan</a>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
