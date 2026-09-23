<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
include '../config/icons.php';
include '../config/layout.php';
requireAdmin();

$username = $_SESSION['username'];
$q        = mysqli_query($conn, "SELECT * FROM admin WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
$admin    = mysqli_fetch_assoc($q);
$error    = '';
$sukses   = '';

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $lama = $_POST['password_lama'] ?? '';
    $baru = $_POST['password_baru'] ?? '';

    if (!$nama) {
        $error = 'Nama tidak boleh kosong.';
    } elseif ($baru !== '' && !password_verify($lama, $admin['password'])) {
        $error = 'Password lama yang Anda masukkan salah.';
    } else {
        $namaEsc = mysqli_real_escape_string($conn, $nama);
        if ($baru !== '') {
            $hash = password_hash($baru, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE admin SET nama='$namaEsc', password='$hash' WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
        } else {
            mysqli_query($conn, "UPDATE admin SET nama='$namaEsc' WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
        }
        $_SESSION['nama'] = $nama;
        $sukses = 'Pengaturan berhasil disimpan.';
        $q     = mysqli_query($conn, "SELECT * FROM admin WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
        $admin = mysqli_fetch_assoc($q);
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pengaturan — SIGAP SARPRAS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
<div class="app">
    <?= sidebar('pengaturan') ?>
    <main class="main">
        <div class="top">
            <div>
                <h2>Pengaturan Akun</h2>
                <p>Kelola profil dan keamanan akun admin.</p>
            </div>
            <div class="top-actions">
                <div class="bell"><?= icon('bell', '17') ?></div>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:260px minmax(0,1fr);gap:20px;margin-top:4px;align-items:start;">

            <div class="panel" style="text-align:center;padding:28px 20px;">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,var(--primary),var(--purple));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#fff;margin:0 auto 14px;box-shadow:0 6px 20px rgba(79,70,229,.3);">
                    <?= strtoupper(substr($admin['nama'], 0, 1)) ?>
                </div>
                <div style="font-size:17px;font-weight:700;"><?= htmlspecialchars($admin['nama']) ?></div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px;">@<?= htmlspecialchars($admin['username']) ?></div>
                <div style="margin-top:12px;">
                    <span class="status selesai">Admin</span>
                </div>
                <div class="divider"></div>
                <div style="font-size:12px;color:var(--muted);text-align:left;">
                    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--line);">
                        <span>Role</span><strong style="color:var(--text);">Koordinator Sarpras</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:6px 0;">
                        <span>Status</span><strong style="color:var(--green);">Aktif</strong>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h3 style="margin:0 0 6px;">Edit Profil Admin</h3>
                <p style="margin:0 0 24px;font-size:13px;color:var(--muted);">Perbarui nama tampilan dan password akun Anda.</p>

                <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($sukses): ?><div class="alert success"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>

                <form method="post">
                    <div class="field">
                        <label>Username</label>
                        <input value="<?= htmlspecialchars($admin['username']) ?>" disabled>
                        <div style="font-size:11px;color:var(--muted);margin-top:5px;">Username tidak dapat diubah.</div>
                    </div>

                    <div class="field">
                        <label>Nama Tampilan <span class="required">*</span></label>
                        <input name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" placeholder="Masukkan nama Anda" required>
                    </div>

                    <div class="divider"></div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;color:var(--text-2);">
                        <?= icon('key', '15') ?>
                        <strong style="font-size:13px;">Ubah Password</strong>
                    </div>

                    <div class="field">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" placeholder="Masukkan password saat ini (isi jika ingin ubah)">
                    </div>

                    <div class="field">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>

                    <div class="form-actions">
                        <button class="btn primary" name="simpan">
                            <span style="display:flex;align-items:center;gap:6px;"><?= icon('save', '14') ?> Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
