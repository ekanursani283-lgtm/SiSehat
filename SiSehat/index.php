<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Admin') {
        header('Location: pages/admin_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'Pasien') {
        header('Location: pages/dashboard_pasien.php');
        exit;
    }
}
include 'home.php';
?>
