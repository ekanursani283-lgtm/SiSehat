<?php
require_once '../config/auth.php';
requireLogin('../');
if ($_SESSION['role'] !== 'Pasien') { header('Location: ../index.php'); exit; }

require_once '../config/koneksi.php';
include '../config/header.php';

$idPasien = (int) $_SESSION['id_pasien'];

$sql = "SELECT rm.diagnosa, rm.resep_obat, rm.catatan, rm.tgl_periksa, d.nama_dokter
        FROM rekam_medis rm
        JOIN janji_temu jt ON rm.id_janji = jt.id_janji
        JOIN dokter d ON jt.id_dokter = d.id_dokter
        WHERE jt.id_pasien = $idPasien
        ORDER BY rm.tgl_periksa DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="page-head">
  <h1>Rekam Medis Saya</h1>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Tanggal Periksa</th>
        <th>Dokter</th>
        <th>Diagnosa</th>
        <th>Resep Obat</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= date('d M Y', strtotime($row['tgl_periksa'])) ?></td>
            <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
            <td><?= htmlspecialchars($row['diagnosa']) ?></td>
            <td><?= htmlspecialchars($row['resep_obat']) ?></td>
            <td><?= htmlspecialchars($row['catatan']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="empty-state">Anda belum memiliki rekam medis.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../config/footer.php'; ?>
