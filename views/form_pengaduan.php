<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireLogin();
include '../models/Pengaduan.php';

$admin = $_SESSION['role'] === 'admin';
$students = mysqli_query($conn, $admin
    ? "SELECT nis, nama, kelas FROM siswa WHERE aktif=1 ORDER BY nama"
    : "SELECT nis, nama, kelas FROM siswa WHERE username='" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'"
);
$cats  = mysqli_query($conn, "SELECT * FROM kategori WHERE aktif=1 ORDER BY nama_kategori");
$error = '';

if (isset($_POST['simpan'])) {
    $nis    = (int)$_POST['nis'];
    $kat    = (int)$_POST['id_kategori'];
    $lokasi = trim($_POST['lokasi']);
    $ket    = trim($_POST['keterangan']);
    $foto   = null;

    if (!$lokasi || !$ket) {
        $error = 'Lengkapi semua data pengaduan yang wajib diisi.';
    } else {
        if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $dir = __DIR__ . '/../assets/uploads';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fname = 'pengaduan_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir . '/' . $fname)) $foto = $fname;
            } else {
                $error = 'Format foto tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
            }
        }
        if (!$error) {
            $m = new Pengaduan($conn);
            if ($m->tambah($nis, $kat, $lokasi, $ket, $foto)) {
                header('Location: ' . ($admin ? 'admin_dashboard.php' : 'siswa_dashboard.php'));
                exit;
            }
            $error = 'Gagal menyimpan pengaduan. Silakan coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Buat Pengaduan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar($admin ? 'complaints' : 'form') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Buat Pengaduan Baru</h2>
                <p>Laporkan kerusakan sarana prasarana sekolah secara lengkap dan jelas.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="form-layout">
            <div class="panel form-panel">
                <h3>Form Pengaduan</h3>

                <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <?php if ($admin): ?>
                    <div class="field">
                        <label>Siswa <span class="required">*</span></label>
                        <select name="nis" required>
                            <option value="">— Pilih siswa —</option>
                            <?php while ($s = mysqli_fetch_assoc($students)): ?>
                            <option value="<?= $s['nis'] ?>"><?= htmlspecialchars($s['nama']) ?> — <?= htmlspecialchars($s['kelas']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php else: $s = mysqli_fetch_assoc($students); ?>
                    <input type="hidden" name="nis" value="<?= $s['nis'] ?>">
                    <?php endif; ?>

                    <div class="field">
                        <label>Kategori Sarana <span class="required">*</span></label>
                        <select name="id_kategori" required>
                            <option value="">— Pilih kategori —</option>
                            <?php while ($c = mysqli_fetch_assoc($cats)): ?>
                            <option value="<?= $c['id_kategori'] ?>"><?= htmlspecialchars($c['nama_kategori']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Lokasi / Ruangan <span class="required">*</span></label>
                        <input name="lokasi" placeholder="Contoh: Lab Komputer 3, Kelas XII A, Toilet Lantai 2" required>
                    </div>

                    <div class="field">
                        <label>Keterangan Kerusakan <span class="required">*</span></label>
                        <textarea name="keterangan" placeholder="Jelaskan secara detail kerusakan atau masalah yang terjadi." required></textarea>
                    </div>

                    <div class="field">
                        <label>Foto Kerusakan <span style="color:var(--muted);font-weight:400;">(opsional)</span></label>
                        <div class="upload">
                            <input type="file" name="foto" accept="image/*" style="width:100%;cursor:pointer;">
                            <div style="margin-top:8px;font-size:12px;color:var(--muted);">Format: JPG, PNG, GIF, WEBP</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn" type="reset">Reset</button>
                        <a class="btn" href="<?= $admin ? 'admin_dashboard.php' : 'siswa_dashboard.php' ?>">Batal</a>
                        <button class="btn primary" name="simpan">
                            <span style="display:flex;align-items:center;gap:6px;"><?= icon('send', '14') ?> Kirim Pengaduan</span>
                        </button>
                    </div>
                </form>
            </div>

            <aside>
                <div class="info-card">
                    <h4>Panduan Pengisian</h4>
                    <p>Pilih kategori sarana yang paling sesuai.</p>
                    <p>Tulis lokasi secara spesifik agar mudah ditemukan.</p>
                    <p>Jelaskan kerusakan sejelas mungkin.</p>
                    <p>Sertakan foto untuk mempercepat verifikasi.</p>
                    <p>Pantau status di menu Riwayat Pengaduan.</p>
                </div>

                <div class="panel" style="margin-top:16px;padding:18px;">
                    <h4 style="margin:0 0 12px;font-size:14px;font-weight:700;">Keterangan Status</h4>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="status">Menunggu</span>
                            <span style="color:var(--muted);">Belum diverifikasi</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="status proses">Diproses</span>
                            <span style="color:var(--muted);">Sedang ditangani</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="status selesai">Selesai</span>
                            <span style="color:var(--muted);">Telah diselesaikan</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</div>
</body>
</html>
