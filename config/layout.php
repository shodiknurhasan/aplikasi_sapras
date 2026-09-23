<?php
include_once __DIR__ . '/icons.php';

function sidebar($active) {
    $role    = $_SESSION['role'] ?? 'siswa';
    $nama    = $_SESSION['nama'] ?? '';
    $initial = strtoupper(substr($nama ?: 'A', 0, 1));

    if ($role === 'admin') {
        $menu = [
            'dashboard'  => ['label' => 'Dashboard',       'href' => 'admin_dashboard.php',  'icon' => 'dashboard'],
            'complaints' => ['label' => 'Semua Pengaduan', 'href' => 'semua_pengaduan.php',  'icon' => 'complaints'],
            'categories' => ['label' => 'Kategori Sarana', 'href' => 'kategori.php',         'icon' => 'category'],
            'students'   => ['label' => 'Data Siswa',      'href' => 'siswa.php',            'icon' => 'students'],
            'laporan'    => ['label' => 'Laporan',         'href' => 'laporan.php',          'icon' => 'report'],
            'pengaturan' => ['label' => 'Pengaturan',      'href' => 'pengaturan.php',       'icon' => 'settings'],
        ];
        $sub = 'Admin · Koordinator Sarpras';
    } else {
        $menu = [
            'dashboard' => ['label' => 'Dashboard',         'href' => 'siswa_dashboard.php', 'icon' => 'dashboard'],
            'form'      => ['label' => 'Buat Pengaduan',    'href' => 'form_pengaduan.php',  'icon' => 'form'],
            'history'   => ['label' => 'Riwayat',           'href' => 'riwayat.php',         'icon' => 'history'],
            'tanggapan' => ['label' => 'Tanggapan',         'href' => 'siswa_tanggapan.php', 'icon' => 'reply'],
        ];
        $kelas = $_SESSION['kelas'] ?? '';
        $sub   = 'Siswa' . ($kelas ? ' · ' . htmlspecialchars($kelas) : '');
    }

    ob_start();
    ?>
    <aside class="sidebar">

        <!-- Logo — di mobile jadi inline row -->
        <div class="logo">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20"/>
                    <path d="M9 22V12h6v10"/>
                    <path d="M8 7h1"/><path d="M15 7h1"/>
                    <path d="M8 11h1"/><path d="M15 11h1"/>
                </svg>
            </div>
            <h1>SIGAP SARPRAS</h1>
            <span>Sistem Pengaduan Sarana Prasarana</span>
        </div>

        <!-- Nav -->
        <nav class="nav">
            <?php if ($role === 'admin'): ?>
            <div class="nav-section">Menu Utama</div>
            <?php endif; ?>

            <?php foreach ($menu as $key => $item): ?>
            <a href="<?= $item['href'] ?>" class="<?= $active === $key ? 'active' : '' ?>">
                <span class="nav-icon"><?= icon($item['icon'], '16') ?></span>
                <?= htmlspecialchars($item['label']) ?>
            </a>
            <?php endforeach; ?>

            <!-- Tombol logout khusus mobile (muncul di ujung nav bar) -->
            <a href="../logout.php" class="nav-logout-mobile" title="Logout">
                <?= icon('logout', '14') ?>
                <span>Logout</span>
            </a>
        </nav>

        <!-- Profil + Logout (desktop) -->
        <div class="sidebar-profile">
            <div class="sidebar-avatar"><?= htmlspecialchars($initial) ?></div>
            <div class="sidebar-info">
                <strong><?= htmlspecialchars($nama) ?></strong>
                <span><?= $sub ?></span>
            </div>
            <a class="sidebar-logout" href="../logout.php" title="Logout">
                <?= icon('logout', '15') ?>
                <span class="logout-label">Logout</span>
            </a>
        </div>

    </aside>
    <?php
    return ob_get_clean();
}
?>
