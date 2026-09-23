<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['role'])) {
        header("Location: ../index.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../views/siswa_dashboard.php");
        exit;
    }
}

function requireSiswa() {
    requireLogin();
    if ($_SESSION['role'] !== 'siswa') {
        header("Location: ../views/admin_dashboard.php");
        exit;
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
