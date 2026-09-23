<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();

$perKategori = mysqli_query($conn,
    "SELECT k.nama_kategori, COUNT(p.id_pengaduan) total
     FROM kategori k LEFT JOIN pengaduan p ON p.id_kategori = k.id_kategori
     GROUP BY k.id_kategori ORDER BY total DESC");

$perStatus = mysqli_query($conn, "SELECT status, COUNT(*) total FROM pengaduan GROUP BY status");

$statusMap = ['Menunggu'=>0,'Diverifikasi'=>0,'Diproses'=>0,'Selesai'=>0,'Ditolak'=>0];
while ($r = mysqli_fetch_assoc($perStatus)) $statusMap[$r['status']] = (int)$r['total'];

$totalAll   = array_sum($statusMap);
$selesaiPct = $totalAll > 0 ? round($statusMap['Selesai'] / $totalAll * 100) : 0;
$prosesPct  = $totalAll > 0 ? round(($statusMap['Diproses'] + $statusMap['Diverifikasi']) / $totalAll * 100) : 0;

$statusClass = ['Menunggu'=>'','Diverifikasi'=>'verified','Diproses'=>'proses','Selesai'=>'selesai','Ditolak'=>'ditolak'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('laporan') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Laporan &amp; Statistik</h2>
                <p>Ringkasan dan analisis pengaduan sarana prasarana sekolah.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="cards">
            <div class="stat-card">
                <div class="stat-icon blue"><?= icon('complaints', '22') ?></div>
                <div>
                    <div class="label">Total Laporan</div>
                    <div class="num"><?= $totalAll ?></div>
                    <div class="sub">semua pengaduan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><?= icon('check', '22') ?></div>
                <div>
                    <div class="label">Diselesaikan</div>
                    <div class="num"><?= $statusMap['Selesai'] ?></div>
                    <div class="sub"><?= $selesaiPct ?>% dari total</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><?= icon('tool', '22') ?></div>
                <div>
                    <div class="label">Sedang Berjalan</div>
                    <div class="num"><?= $statusMap['Diproses'] + $statusMap['Diverifikasi'] ?></div>
                    <div class="sub"><?= $prosesPct ?>% dari total</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><?= icon('clock', '22') ?></div>
                <div>
                    <div class="label">Menunggu</div>
                    <div class="num"><?= $statusMap['Menunggu'] ?></div>
                    <div class="sub">perlu tindak lanjut</div>
                </div>
            </div>
        </div>

        <div class="panel mt-28" style="padding:22px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <div>
                    <strong style="font-size:15px;">Tingkat Penyelesaian</strong>
                    <p style="margin:3px 0 0;font-size:12px;color:var(--muted);">Persentase pengaduan yang telah diselesaikan</p>
                </div>
                <div style="font-size:28px;font-weight:800;color:var(--green);"><?= $selesaiPct ?>%</div>
            </div>
            <div style="height:10px;background:var(--bg-2);border-radius:999px;overflow:hidden;border:1px solid var(--line);">
                <div style="height:100%;width:<?= $selesaiPct ?>%;background:linear-gradient(90deg,var(--green),var(--primary));border-radius:999px;"></div>
            </div>
        </div>

        <div class="grid2 mt-28" style="grid-template-columns:1fr 1fr;">
            <div class="panel table-panel">
                <h3>Pengaduan per Status</h3>
                <p>Distribusi semua pengaduan berdasarkan statusnya.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Status</th><th>Jumlah</th><th>Persentase</th></tr></thead>
                        <tbody>
                            <?php foreach ($statusMap as $s => $t): ?>
                            <tr>
                                <td><span class="status <?= $statusClass[$s] ?? '' ?>"><?= $s ?></span></td>
                                <td><strong><?= $t ?></strong></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="flex:1;height:6px;background:var(--bg-2);border-radius:999px;overflow:hidden;min-width:60px;">
                                            <div style="height:100%;width:<?= $totalAll>0 ? round($t/$totalAll*100) : 0 ?>%;background:var(--primary);border-radius:999px;"></div>
                                        </div>
                                        <span style="font-size:12px;color:var(--muted);min-width:30px;"><?= $totalAll>0 ? round($t/$totalAll*100) : 0 ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel table-panel">
                <h3>Pengaduan per Kategori</h3>
                <p>Kategori sarana yang paling sering dilaporkan.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Kategori</th><th>Jumlah</th></tr></thead>
                        <tbody>
                            <?php
                            $rows_kat = [];
                            while ($r = mysqli_fetch_assoc($perKategori)) $rows_kat[] = $r;
                            $maxKat = $rows_kat ? max(array_column($rows_kat, 'total')) : 1;
                            if (!$rows_kat): ?>
                            <tr><td colspan="2" class="empty-state">Belum ada data.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($rows_kat as $r): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:500;"><?= htmlspecialchars($r['nama_kategori']) ?></div>
                                    <div style="height:4px;background:var(--bg-2);border-radius:999px;margin-top:6px;overflow:hidden;">
                                        <div style="height:100%;width:<?= $maxKat>0 ? round($r['total']/$maxKat*100) : 0 ?>%;background:linear-gradient(90deg,var(--primary),var(--purple));border-radius:999px;"></div>
                                    </div>
                                </td>
                                <td style="font-weight:700;font-size:16px;color:var(--text);"><?= $r['total'] ?></td>
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
