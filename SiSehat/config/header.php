<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SiSehat - Sistem Informasi Klinik</title>
<link rel="stylesheet" href="/SiSehat/assets/css/style.css">
</head>
<body>

<div class="navbar">
    <div class="navbar-brand">🏥 SiSehat</div>

    <nav class="navbar-menu">

        <?php if ($role === 'Admin'): ?>

            <a href="/SiSehat/pages/admin_dashboard.php">Dashboard</a>
            <a href="/SiSehat/pages/pasien/index.php">Pasien</a>
            <a href="/SiSehat/pages/dokter/index.php">Dokter</a>
            <a href="/SiSehat/pages/janji_temu/index.php">Janji Temu</a>
            <a href="/SiSehat/pages/rekam_medis/index.php">Rekam Medis</a>
            <a href="/SiSehat/pages/obat/index.php">Obat</a>
            <a href="/SiSehat/pages/pembayaran/index.php">Pembayaran</a>
            <a href="/SiSehat/pages/laporan.php">Laporan</a>
            <a href="/SiSehat/pages/users/index.php">Kelola User</a>

        <?php elseif ($role === 'Pasien'): ?>

            <a href="/SiSehat/pages/dashboard_pasien.php">Dashboard Saya</a>
            <a href="/SiSehat/pages/jadwal_dokter.php">Jadwal Dokter</a>
            <a href="/SiSehat/pages/janji_temu_saya.php">Janji Temu Saya</a>
            <a href="/SiSehat/pages/rekam_medis_saya.php">Rekam Medis Saya</a>

        <?php else: ?>

            <a href="/SiSehat/home.php">Beranda</a>
            <a href="/SiSehat/pages/jadwal_dokter.php">Dokter</a>
            <a href="/SiSehat/pages/layanan.php">Layanan</a>

        <?php endif; ?>

    </nav>

    <?php if ($role): ?>

        <div class="navbar-user">
            <span>
                <?= htmlspecialchars($_SESSION['username']) ?>
                <span class="role-badge">
                    <?= htmlspecialchars($role) ?>
                </span>
            </span>

            <a href="/SiSehat/logout.php" class="btn btn-sm btn-light">
                Logout
            </a>
        </div>

    <?php else: ?>

        <div class="navbar-user">
            <a href="/SiSehat/login.php" class="btn btn-primary btn-sm">
                🔐 Masuk
            </a>
        </div>

    <?php endif; ?>

</div>

<div class="container">