<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireSiswa();
include '../models/Pengaduan.php';

$q    = mysqli_query($conn, "SELECT nis, nama FROM siswa WHERE username='" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'");
$akun = mysqli_fetch_assoc($q);
$nis  = (int)$akun['nis'];

$m = new Pengaduan($conn);

// ── Handle hapus ─────────────────────────────────────────
$msg_hapus = '';
$err_hapus = false;

if (isset($_GET['hapus'])) {
    $result    = $m->hapusSiswa((int)$_GET['hapus'], $nis);
    $msg_hapus = $result['message'];
    $err_hapus = !$result['ok'];
}

// ── Ambil data ───────────────────────────────────────────
$rs   = $m->siswa($nis);
$rows = [];
while ($r = mysqli_fetch_assoc($rs)) $rows[] = $r;

$filter = $_GET['status'] ?? '';
if ($filter) $rows = array_values(array_filter($rows, fn($r) => $r['status'] === $filter));
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Riwayat Pengaduan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('history') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Riwayat Pengaduan</h2>
                <p>Semua laporan yang pernah Anda ajukan · <?= count($rows) ?> pengaduan<?= $filter ? " · filter: $filter" : '' ?></p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($akun['nama'], 0, 1)) ?></div>
            </div>
        </div>

        <?php if ($msg_hapus): ?>
        <div class="alert <?= $err_hapus ? 'error' : 'success' ?>"><?= htmlspecialchars($msg_hapus) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert success">Pengaduan berhasil dihapus.</div>
        <?php endif; ?>

        <div class="panel table-panel">
            <div class="toolbar">
                <strong class="section-title">Daftar Pengaduan Saya</strong>
                <div class="toolbar-actions">
                    <a class="btn <?= $filter==='' ? 'primary' : '' ?>" href="riwayat.php">Semua</a>
                    <a class="btn <?= $filter==='Menunggu' ? 'primary' : '' ?>" href="riwayat.php?status=Menunggu">Menunggu</a>
                    <a class="btn <?= $filter==='Diproses' ? 'primary' : '' ?>" href="riwayat.php?status=Diproses">Diproses</a>
                    <a class="btn <?= $filter==='Selesai' ? 'primary' : '' ?>" href="riwayat.php?status=Selesai">Selesai</a>
                    <a href="form_pengaduan.php" class="btn primary">
                        <span style="display:flex;align-items:center;gap:6px;"><?= icon('plus', '13') ?> Buat Baru</span>
                    </a>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$rows): ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <?= $filter
                                    ? "Tidak ada pengaduan dengan status \"" . htmlspecialchars($filter) . "\"."
                                    : "Belum ada pengaduan." ?>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($rows as $r):
                            $bisa_hapus = in_array($r['status'], ['Menunggu', 'Ditolak']);
                            $warn = $r['status'] === 'Menunggu'
                                ? "Hapus pengaduan ini?\n\nPengaduan yang dihapus tidak dapat dikembalikan."
                                : "Hapus pengaduan ini?\n\nPengaduan yang dihapus tidak dapat dikembalikan.";
                        ?>
                        <tr>
                            <td style="font-weight:600;color:var(--muted);font-size:12px;">
                                #<?= str_pad($r['id_pengaduan'], 4, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td>
                                <div style="font-weight:600;color:var(--text);"><?= htmlspecialchars($r['nama_kategori']) ?></div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($r['lokasi']) ?></div>
                            </td>
                            <td style="font-size:12px;color:var(--muted);"><?= htmlspecialchars($r['lokasi']) ?></td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                                <?= date('d M Y', strtotime($r['created_at'])) ?>
                            </td>
                            <td>
                                <span class="status <?= $r['status']==='Selesai' ? 'selesai' : ($r['status']==='Diproses' ? 'proses' : ($r['status']==='Ditolak' ? 'ditolak' : '')) ?>">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-row" style="justify-content:center;">
                                    <a class="action-btn" href="detail_pengaduan.php?id=<?= $r['id_pengaduan'] ?>">
                                        <span style="display:flex;align-items:center;gap:4px;">
                                            <?= icon('eye', '13') ?> Lihat
                                        </span>
                                    </a>

                                    <?php if ($bisa_hapus): ?>
                                    <a class="action-btn danger"
                                       href="riwayat.php?hapus=<?= $r['id_pengaduan'] ?><?= $filter ? '&status='.urlencode($filter) : '' ?>"
                                       onclick="return confirm('<?= addslashes($warn) ?>')">
                                        <span style="display:flex;align-items:center;gap:4px;">
                                            <?= icon('trash', '13') ?> Hapus
                                        </span>
                                    </a>
                                    <?php else: ?>
                                    <span style="font-size:11px;color:var(--muted-light);padding:6px 4px;">
                                        Tidak dapat dihapus
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:12px;font-size:12px;color:var(--muted);display:flex;align-items:flex-start;gap:7px;line-height:1.5;">
            <?= icon('info', '14') ?>
            <span>
                Pengaduan berstatus <strong style="color:var(--amber-dark);">Menunggu</strong> atau
                <strong style="color:#9f1239;">Ditolak</strong> dapat dihapus.
                Pengaduan yang sedang <strong>Diproses</strong> atau sudah <strong>Selesai</strong> tidak dapat dihapus.
            </span>
        </div>
    </main>
</div>
</body>
</html>
