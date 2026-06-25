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
<title>Klinik Sani Sehat - SiSehat</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;color:#14302F;background:#F4FBFB;}
.navbar{display:flex;align-items:center;justify-content:space-between;padding:18px 40px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.navbar-brand{font-family:'Montserrat',sans-serif;font-size:22px;font-weight:800;color:#00A3A6;}
.navbar-menu a{margin-left:28px;text-decoration:none;color:#4B6664;font-weight:600;font-size:15px;}
.navbar-menu a:hover{color:#00A3A6;}
.btn{display:inline-block;padding:12px 28px;background:#00A3A6;color:#fff;border-radius:999px;text-decoration:none;font-weight:700;font-family:'Montserrat',sans-serif;font-size:15px;transition:all 0.2s;}
.btn:hover{background:#007A7D;transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,163,166,0.35);}
.btn-light{background:#fff;color:#00A3A6;border:2px solid #00A3A6;}
.btn-light:hover{background:#E8F8F8;}
.hero{padding:80px 40px;text-align:center;background:linear-gradient(135deg,#00A3A6,#007A7D);color:#fff;}
.hero h1{font-family:'Montserrat',sans-serif;font-size:42px;font-weight:800;margin-bottom:16px;}
.hero p{font-size:17px;opacity:0.95;max-width:560px;margin:0 auto 32px;line-height:1.6;}
.hero .actions{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;}
.section{padding:64px 40px;max-width:1000px;margin:0 auto;}
.section h2{font-family:'Montserrat',sans-serif;font-size:28px;font-weight:800;margin-bottom:24px;text-align:center;color:#14302F;}
.info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:32px;}
.info-card{background:#fff;border-radius:18px;padding:28px;text-align:center;box-shadow:0 4px 16px rgba(0,0,0,0.05);}
.info-card .icon{font-size:32px;margin-bottom:12px;}
.info-card h3{font-size:16px;font-weight:700;margin-bottom:8px;}
.info-card p{font-size:14px;color:#4B6664;line-height:1.6;}
.cta{text-align:center;padding:60px 40px;background:#fff;}
.cta h2{font-family:'Montserrat',sans-serif;font-size:26px;font-weight:800;margin-bottom:14px;}
.cta p{color:#4B6664;margin-bottom:24px;}
.footer{text-align:center;padding:28px;color:#93a8a6;font-size:13px;background:#F4FBFB;}
@media (max-width:768px){
  .info-grid{grid-template-columns:1fr;}
  .hero h1{font-size:30px;}
  .navbar{padding:16px 20px;}
  .navbar-menu a{margin-left:14px;font-size:13px;}
}
</style>
</head>
<body>

<div class="navbar">
  <div class="navbar-brand">🏥 Klinik Sani Sehat</div>
  <nav class="navbar-menu">
    <a href="pages/jadwal_dokter.php">Jadwal Dokter</a>
    <?php if ($role === 'Admin'): ?>
      <a href="pages/admin_dashboard.php">Dashboard Admin</a>
    <?php elseif ($role === 'Pasien'): ?>
      <a href="pages/dashboard_pasien.php">Dashboard Saya</a>
    <?php else: ?>
      <a href="login.php" class="btn" style="margin-left:20px; padding:8px 20px;">Login</a>
    <?php endif; ?>
  </nav>
</div>

<div class="hero">
  <h1>Layanan Kesehatan Terpercaya<br>untuk Keluarga Anda</h1>
  <p>Klinik Sani Sehat hadir dengan dokter berpengalaman dan sistem booking online yang mudah. Cek jadwal dokter dan buat janji temu kapan saja.</p>
  <div class="actions">
    <a href="pages/jadwal_dokter.php" class="btn">Lihat Jadwal Dokter</a>
    <?php if (!$role): ?>
      <a href="login.php" class="btn btn-light">Login / Daftar</a>
    <?php endif; ?>
  </div>
</div>

<div class="section">
  <h2>Informasi Klinik</h2>
  <div class="info-grid">
    <div class="info-card">
      <div class="icon">📍</div>
      <h3>Alamat</h3>
      <p>Jl. Kesehatan No. 88,<br>Bandar Lampung, Lampung</p>
    </div>
    <div class="info-card">
      <div class="icon">🕒</div>
      <h3>Jam Operasional</h3>
      <p>Senin – Sabtu: 08.00 – 17.00<br>Minggu &amp; Hari Libur: Tutup</p>
    </div>
    <div class="info-card">
      <div class="icon">📞</div>
      <h3>Kontak</h3>
      <p>Telepon: (0721) 123-456<br>WhatsApp: 0812-3456-7890</p>
    </div>
  </div>
</div>

<div class="cta">
  <h2>Siap Berkonsultasi?</h2>
  <p>Cek jadwal dokter kami dan buat janji temu dalam beberapa klik.</p>
  <a href="pages/jadwal_dokter.php" class="btn">Lihat Jadwal Dokter</a>
</div>

<div class="footer">
  &copy; <?= date('Y') ?> Klinik Sani Sehat &middot; Sistem Informasi Pelayanan Kesehatan Klinik (SiSehat)
</div>

</body>
</html>
