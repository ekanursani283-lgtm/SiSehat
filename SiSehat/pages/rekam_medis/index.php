<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$sql = "SELECT rm.id_rekam, rm.diagnosa, rm.resep_obat, rm.catatan, rm.tgl_periksa,
               p.nama_pasien, d.nama_dokter
        FROM rekam_medis rm
        JOIN janji_temu jt ON rm.id_janji = jt.id_janji
        JOIN pasien p ON jt.id_pasien = p.id_pasien
        JOIN dokter d ON jt.id_dokter = d.id_dokter
        ORDER BY rm.tgl_periksa DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="page-head">
  <h1>Rekam Medis</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah Rekam Medis</a>
</div>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan = [
        'tambah' => 'Rekam medis berhasil ditambahkan.',
        'edit'   => 'Rekam medis berhasil diperbarui.',
        'hapus'  => 'Rekam medis berhasil dihapus.'
      ];
      echo $pesan[$_GET['sukses']] ?? 'Berhasil.';
    ?>
  </div>
<?php endif; ?>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Pasien</th>
        <th>Dokter</th>
        <th>Diagnosa</th>
        <th>Resep Obat</th>
        <th>Tgl Periksa</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
            <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
            <td><?= htmlspecialchars($row['diagnosa']) ?></td>
            <td><?= htmlspecialchars($row['resep_obat']) ?></td>
            <td><?= date('d M Y', strtotime($row['tgl_periksa'])) ?></td>
            <td class="actions">
              <a href="edit.php?id=<?= $row['id_rekam'] ?>" class="btn btn-edit btn-sm">Edit</a>
              <a href="hapus.php?id=<?= $row['id_rekam'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin menghapus rekam medis ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="empty-state">Belum ada data rekam medis.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../../config/footer.php'; ?>
