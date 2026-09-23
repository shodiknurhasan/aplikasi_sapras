<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Pengaduan.php';

$m   = new Pengaduan($conn);
$id  = (int)($_GET['id'] ?? 0);
$d   = $m->get($id);
if (!$d) die('Pengaduan tidak ditemukan.');

$error = '';
if (isset($_POST['simpan'])) {
    if ($m->updateStatus($id, $_POST['status'], $_POST['feedback'] ?? '', $_POST['catatan'] ?? '', $_SESSION['username'])) {
        header('Location: tanggapan.php?id=' . $id . '&saved=1');
        exit;
    }
    $error = 'Gagal menyimpan perubahan.';
}
$hist  = $m->penanganan($id);
$saved = isset($_GET['saved']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tanggapan #<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?> — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('complaints') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Detail &amp; Penanganan</h2>
                <p>Verifikasi laporan, ubah status, dan berikan feedback ke siswa.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="detail-top">
            <div class="detail-title">
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--primary);margin-bottom:6px;background:var(--primary-bg);display:inline-block;padding:3px 10px;border-radius:999px;border:1px solid #c7d2fe;">
                        Laporan #<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?>
                    </div>
                    <h3><?= htmlspecialchars($d['nama_kategori']) ?></h3>
                    <div class="meta">
                        <span><?= icon('user', '11') ?> <?= htmlspecialchars($d['nama']) ?> (<?= htmlspecialchars($d['nis']) ?>)</span>
                        <span><?= icon('location', '11') ?> <?= htmlspecialchars($d['lokasi']) ?></span>
                        <span><?= icon('calendar', '11') ?> <?= date('d M Y', strtotime($d['created_at'])) ?></span>
                    </div>
                </div>
                <span class="status <?= $d['status']==='Selesai' ? 'selesai' : ($d['status']==='Diproses' ? 'proses' : ($d['status']==='Ditolak' ? 'ditolak' : '')) ?>" style="font-size:12px;padding:6px 14px;">
                    <?= htmlspecialchars($d['status']) ?>
                </span>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-box">
                <h4>Keterangan Laporan</h4>
                <p class="detail-copy"><?= nl2br(htmlspecialchars($d['keterangan'])) ?></p>

                <?php if (!empty($d['foto'])): ?>
                <h4 class="detail-subtitle">Foto Kerusakan</h4>
                <img class="detail-image" src="../assets/uploads/<?= htmlspecialchars($d['foto']) ?>" alt="Foto kerusakan">
                <?php endif; ?>

                <div class="detail-form">
                    <h4 style="border-top:none;padding-top:0;margin-bottom:16px;">Update Status Pengaduan</h4>

                    <?php if ($saved): ?><div class="alert success">Perubahan berhasil disimpan.</div><?php endif; ?>
                    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

                    <form method="post">
                        <div class="field">
                            <label>Status Pengaduan</label>
                            <select name="status">
                                <?php foreach (['Diverifikasi','Diproses','Selesai','Ditolak'] as $opt): ?>
                                <option <?= $d['status']===$opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Feedback untuk Siswa</label>
                            <textarea name="feedback" placeholder="Contoh: Laporan diterima, perbaikan akan dilakukan dalam 3 hari kerja."></textarea>
                        </div>
                        <div class="field">
                            <label>Catatan Internal <span style="color:var(--muted);font-weight:400;">(tidak ditampilkan ke siswa)</span></label>
                            <textarea name="catatan" placeholder="Catatan untuk arsip internal petugas..."></textarea>
                        </div>
                        <div class="form-actions" style="padding-top:0;border-top:none;margin-top:0;">
                            <a class="btn" href="semua_pengaduan.php">
                                <span style="display:flex;align-items:center;gap:6px;"><?= icon('arrow-left', '14') ?> Kembali</span>
                            </a>
                            <button class="btn primary" name="simpan">
                                <span style="display:flex;align-items:center;gap:6px;"><?= icon('save', '14') ?> Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="detail-box">
                <h4>Riwayat Penanganan</h4>
                <?php $has = false; while ($h = mysqli_fetch_assoc($hist)): $has = true; ?>
                <div class="history-item">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
                        <span class="status <?= $h['status']==='Selesai' ? 'selesai' : ($h['status']==='Diproses' ? 'proses' : ($h['status']==='Ditolak' ? 'ditolak' : '')) ?>">
                            <?= htmlspecialchars($h['status']) ?>
                        </span>
                        <span class="history-date"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></span>
                    </div>
                    <?php if ($h['feedback']): ?>
                    <div class="history-text">
                        <strong style="font-size:11px;color:var(--muted);">Feedback:</strong><br>
                        <?= nl2br(htmlspecialchars($h['feedback'])) ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($h['catatan']): ?>
                    <div class="history-text" style="background:var(--bg-2);border-radius:6px;padding:8px 10px;margin-top:6px;">
                        <strong style="font-size:11px;color:var(--muted);">Catatan internal:</strong><br>
                        <?= nl2br(htmlspecialchars($h['catatan'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
                <?php if (!$has): ?>
                <div style="text-align:center;padding:30px 0;color:var(--muted);">
                    <div style="margin-bottom:8px;"><?= icon('list', '32') ?></div>
                    <div>Belum ada riwayat penanganan.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
