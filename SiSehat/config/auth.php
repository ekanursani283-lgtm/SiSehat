<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireLogin($base = '') {
    if (!isset($_SESSION['id_user'])) {
        header('Location: ' . $base . 'login.php');
        exit;
    }
}

function requireAdmin($base = '') {
    requireLogin($base);
    if ($_SESSION['role'] !== 'Admin') {
        header('Location: ' . $base . 'pages/dashboard_pasien.php');
        exit;
    }
}