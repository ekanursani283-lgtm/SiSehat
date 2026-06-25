<?php
require_once '../config/auth.php';
requireLogin('../');
if ($_SESSION['role'] !== 'Pasien') { header('Location: ../index.php'); exit; }

require_once '../config/koneksi.php';
include '../config/header.php';

$idPasien = (int) $_SESSION['id_pasien'];

$sql = "SELECT jt.id_janji, d.nama_dokter, d.spesialisasi, jt.tgl_janji, jt.keluhan, jt.status
        FROM janji_temu jt
        JOIN dokter d ON jt.id_dokter = d.id_dokter
        WHERE jt.id_pasien = $idPasien
        ORDER BY jt.tgl_janji DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="page-head">
  <h1>Janji Temu Saya</h1>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Dokter</th>
        <th>Spesialisasi</th>
        <th>Tanggal &amp; Jam</th>
        <th>Keluhan</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
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
        <tr><td colspan="5" class="empty-state">Anda belum memiliki janji temu.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../config/footer.php'; ?>
