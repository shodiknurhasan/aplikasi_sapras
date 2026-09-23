<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();
include '../models/Pengaduan.php';

$m    = new Pengaduan($conn);
$q    = $m->semua();
$rows = [];
while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;

$filter = $_GET['status'] ?? '';
if ($filter) {
    $rows = array_values(array_filter($rows, fn($r) => $r['status'] === $filter));
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Semua Pengaduan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('complaints') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Semua Pengaduan</h2>
                <p>Daftar seluruh pengaduan sarana prasarana · <?= count($rows) ?> laporan<?= $filter ? " · filter: $filter" : '' ?></p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="panel table-panel">
            <div class="toolbar">
                <strong class="section-title">Daftar Pengaduan</strong>
                <div class="toolbar-actions">
                    <a class="btn <?= $filter==='' ? 'primary' : '' ?>" href="semua_pengaduan.php">Semua</a>
                    <a class="btn <?= $filter==='Menunggu' ? 'primary' : '' ?>" href="semua_pengaduan.php?status=Menunggu">Menunggu</a>
                    <a class="btn <?= $filter==='Diproses' ? 'primary' : '' ?>" href="semua_pengaduan.php?status=Diproses">Diproses</a>
                    <a class="btn <?= $filter==='Selesai' ? 'primary' : '' ?>" href="semua_pengaduan.php?status=Selesai">Selesai</a>
                    <a class="btn <?= $filter==='Ditolak' ? 'primary' : '' ?>" href="semua_pengaduan.php?status=Ditolak">Ditolak</a>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Pelapor</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$rows): ?>
                        <tr><td colspan="7" class="empty-state"><?= $filter ? "Tidak ada pengaduan dengan status \"$filter\"." : "Belum ada pengaduan." ?></td></tr>
                        <?php endif; ?>
                        <?php foreach ($rows as $r): ?>
                        <tr>
                            <td style="font-weight:600;color:var(--muted);font-size:12px;">#<?= str_pad($r['id_pengaduan'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div style="font-weight:600;color:var(--text);"><?= htmlspecialchars($r['nama']) ?></div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($r['kelas']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($r['nama_kategori']) ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?= htmlspecialchars($r['lokasi']) ?></td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
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
