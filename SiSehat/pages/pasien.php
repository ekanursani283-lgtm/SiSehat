<?php
require_once '../config/auth.php';
requireLogin('../');
if ($_SESSION['role'] !== 'Pasien') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/koneksi.php';
include '../config/header.php';

$ip = (int) ($_SESSION['id_pasien'] ?? 0);
$pas = $ip ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pasien WHERE id_pasien = $ip")) : null;
$nama = $pas['nama_pasien'] ?? 'Pasien';
$jm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) n FROM janji_temu WHERE id_pasien = $ip AND status = 'Menunggu'"))['n'];
$js = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) n FROM janji_temu WHERE id_pasien = $ip AND status = 'Selesai'"))['n'];
$r = mysqli_query($conn, "SELECT jt.*, d.nama_dokter, d.spesialisasi FROM janji_temu jt JOIN dokter d ON jt.id_dokter = d.id_dokter WHERE jt.id_pasien = $ip ORDER BY jt.tgl_janji DESC LIMIT 5");
?>

<div class="page-head">
  <h1>Halo, <?= htmlspecialchars($nama) ?> 👋</h1>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="num"><?= $jm ?></div>
    <div class="label">📅 Janji Menunggu</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $js ?></div>
    <div class="label">✅ Kunjungan Selesai</div>
  </div>
</div>

<div class="card">
  <h2>Riwayat Janji Temu Terbaru</h2>
  <table>
    <thead>
      <tr>
        <th>Dokter</th>
        <th>Spesialisasi</th>
        <th>Tanggal</th>
        <th>Keluhan</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($r) > 0): while ($row = mysqli_fetch_assoc($r)): ?>
      <tr>
        <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
        <td><?= htmlspecialchars($row['spesialisasi']) ?></td>
        <td><?= date('d M Y H:i', strtotime($row['tgl_janji'])) ?></td>
        <td><?= htmlspecialchars($row['keluhan']) ?></td>
        <td>
          <span class="badge badge-<?= $row['status'] === 'Selesai' ? 'selesai' : ($row['status'] === 'Batal' ? 'batal' : 'menunggu') ?>">
            <?= $row['status'] ?>
          </span>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="5" class="empty-state">Belum ada riwayat janji temu.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../config/footer.php'; ?>C:\xampp\htdocs\sisehat\pages\dashboard_pasien.php