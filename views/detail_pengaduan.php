<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireSiswa();
include '../models/Pengaduan.php';

$id = (int)($_GET['id'] ?? 0);
$m  = new Pengaduan($conn);
$d  = $m->get($id);

$qq = mysqli_query($conn, "SELECT nis FROM siswa WHERE username='" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'");
$me = mysqli_fetch_assoc($qq);

if (!$d || $d['nis'] != $me['nis']) die('Pengaduan tidak ditemukan atau akses ditolak.');

$hist  = $m->penanganan($id);
$found = [];
while ($h = mysqli_fetch_assoc($hist)) $found[$h['status']] = $h;

// ── Hapus dari halaman detail ────────────────────────────
if (isset($_GET['hapus']) && $_GET['hapus'] == $id) {
    $result = $m->hapusSiswa($id, (int)$me['nis']);
    if ($result['ok']) {
        header('Location: riwayat.php?deleted=1');
        exit;
    }
    $delete_error = $result['message'];
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Detail Pengaduan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('history') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Detail Pengaduan</h2>
                <p>Informasi lengkap laporan dan perkembangan penanganan.</p>
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
                        Laporan #<?= str_pad($d['id_pengaduan'], 4, '0', STR_PAD_LEFT) ?>
                    </div>
                    <h3><?= htmlspecialchars($d['nama_kategori']) ?></h3>
                    <div class="meta">
                        <span><?= icon('location', '11') ?> <?= htmlspecialchars($d['lokasi']) ?></span>
                        <span><?= icon('calendar', '11') ?> <?= date('d M Y', strtotime($d['created_at'])) ?></span>
                        <span><?= icon('user', '11') ?> NIS <?= htmlspecialchars($d['nis']) ?></span>
                    </div>
                </div>
                <span class="status <?= $d['status']==='Selesai' ? 'selesai' : ($d['status']==='Diproses' ? 'proses' : ($d['status']==='Ditolak' ? 'ditolak' : '')) ?>" style="font-size:12px;padding:6px 14px;">
                    <?= htmlspecialchars($d['status']) ?>
                </span>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-box">
                <h4>Keterangan Kerusakan</h4>
                <p class="detail-copy"><?= nl2br(htmlspecialchars($d['keterangan'])) ?></p>

                <?php if (!empty($d['foto'])): ?>
                <h4 class="detail-subtitle">Foto Dokumentasi</h4>
                <img class="detail-image" src="../assets/uploads/<?= htmlspecialchars($d['foto']) ?>" alt="Foto kerusakan">
                <?php else: ?>
                <div class="no-image" style="margin-top:18px;">Tidak ada foto terlampir</div>
                <?php endif; ?>

                <div class="divider"></div>
                <?php if (!empty($delete_error)): ?>
                <div class="alert error" style="margin-bottom:12px;"><?= htmlspecialchars($delete_error) ?></div>
                <?php endif; ?>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="riwayat.php" class="btn">
                        <span style="display:flex;align-items:center;gap:6px;"><?= icon('arrow-left', '14') ?> Kembali ke Riwayat</span>
                    </a>
                    <?php if ($d['status'] === 'Menunggu'): ?>
                    <a href="detail_pengaduan.php?id=<?= $id ?>&hapus=<?= $id ?>"
                       class="btn danger"
                       onclick="return confirm('Hapus pengaduan ini?\n\nPengaduan yang dihapus tidak dapat dikembalikan.')">
                        <span style="display:flex;align-items:center;gap:6px;"><?= icon('trash', '14') ?> Hapus Pengaduan</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-box">
                <h4>Timeline Penanganan</h4>
                <div class="timeline">
                    <div class="timeline-item done">
                        <h5>Pengaduan Dibuat</h5>
                        <p><?= date('d M Y H:i', strtotime($d['created_at'])) ?> — Laporan berhasil dikirim.</p>
                    </div>
                    <?php foreach (['Diverifikasi','Diproses','Selesai'] as $s):
                        $h    = $found[$s] ?? null;
                        $desc = ['Diverifikasi'=>'Menunggu verifikasi petugas.','Diproses'=>'Belum mulai diproses.','Selesai'=>'Penanganan belum selesai.'];
                    ?>
                    <div class="timeline-item <?= $h ? 'done' : '' ?>">
                        <h5><?= $s ?></h5>
                        <p><?= $h ? date('d M Y H:i', strtotime($h['created_at'])) . ($h['feedback'] ? ' — ' . htmlspecialchars($h['feedback']) : '') : $desc[$s] ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php if (isset($found['Ditolak'])): ?>
                    <div class="timeline-item">
                        <h5 style="color:var(--danger);">Ditolak</h5>
                        <p><?= date('d M Y H:i', strtotime($found['Ditolak']['created_at'])) ?><?= $found['Ditolak']['feedback'] ? ' — ' . htmlspecialchars($found['Ditolak']['feedback']) : '' ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
