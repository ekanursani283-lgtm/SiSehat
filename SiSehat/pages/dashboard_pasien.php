<?php
require_once '../config/auth.php';
requireLogin('../');
if ($_SESSION['role'] !== 'Pasien') { header('Location: ../index.php'); exit; }

require_once '../config/koneksi.php';
include '../config/header.php';

$idPasien = (int) $_SESSION['id_pasien'];

$pasien = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM pasien WHERE id_pasien = $idPasien"));

$jmlJanjiMenunggu = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS n FROM janji_temu WHERE id_pasien = $idPasien AND status = 'Menunggu'"))['n'];
$jmlJanjiSelesai = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS n FROM janji_temu WHERE id_pasien = $idPasien AND status = 'Selesai'"))['n'];

$sqlJanji = "SELECT jt.id_janji, d.nama_dokter, d.spesialisasi, jt.tgl_janji, jt.keluhan, jt.status
             FROM janji_temu jt
             JOIN dokter d ON jt.id_dokter = d.id_dokter
             WHERE jt.id_pasien = $idPasien
             ORDER BY jt.tgl_janji DESC
             LIMIT 5";
$resJanji = mysqli_query($conn, $sqlJanji);
?>

<div class="page-head">
  <h1>Halo, <?= htmlspecialchars($pasien['nama_pasien']) ?> 👋</h1>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="num"><?= $jmlJanjiMenunggu ?></div>
    <div class="label">Janji Temu Menunggu</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $jmlJanjiSelesai ?></div>
    <div class="label">Kunjungan Selesai</div>
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
      <?php if (mysqli_num_rows($resJanji) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($resJanji)): ?>
          <tr>
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
        <tr><td colspan="5" class="empty-state">Belum ada riwayat janji temu.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../config/footer.php'; ?>
