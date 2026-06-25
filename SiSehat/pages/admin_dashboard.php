<?php
require_once '../config/auth.php';
requireAdmin('../');
require_once '../config/koneksi.php';
include '../config/header.php';

$jmlPasien   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM pasien"))['n'];
$jmlDokter   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM dokter"))['n'];
$jmlJanji    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM janji_temu WHERE status = 'Menunggu'"))['n'];
$jmlObat     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM obat"))['n'];

$sqlJanji = "SELECT jt.id_janji, p.nama_pasien, d.nama_dokter, d.spesialisasi, jt.tgl_janji, jt.keluhan, jt.status
             FROM janji_temu jt
             JOIN pasien p ON jt.id_pasien = p.id_pasien
             JOIN dokter d ON jt.id_dokter = d.id_dokter
             ORDER BY jt.tgl_janji DESC
             LIMIT 5";
$resJanji = mysqli_query($conn, $sqlJanji);
?>

<div class="page-head">
  <h1>Halo, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="num"><?= $jmlPasien ?></div>
    <div class="label">🧑‍🤝‍🧑 Total Pasien</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $jmlDokter ?></div>
    <div class="label">🩺 Total Dokter</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $jmlJanji ?></div>
    <div class="label">📅 Janji Temu Menunggu</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $jmlObat ?></div>
    <div class="label">💊 Jenis Obat</div>
  </div>
</div>

<div class="card">
  <h2>Janji Temu Terbaru</h2>
  <table>
    <thead>
      <tr>
        <th>Pasien</th>
        <th>Dokter</th>
        <th>Spesialisasi</th>
        <th>Tanggal</th>
        <th>Keluhan</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($resJanji) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($resJanji)): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
            <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
            <td><?= htmlspecialchars($row['spesialisasi']) ?></td>
            <td><?= date('d M Y, H:i', strtotime($row['tgl_janji'])) ?></td>
            <td><?= htmlspecialchars($row['keluhan']) ?></td>
            <td>
              <?php
                $cls = $row['status'] === 'Selesai' ? 'badge-selesai' : ($row['status'] === 'Batal' ? 'badge-batal' : 'badge-menunggu');
              ?>
              <span class="badge <?= $cls ?>"><?= $row['status'] ?></span>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="empty-state">Belum ada data janji temu.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../config/footer.php'; ?>

