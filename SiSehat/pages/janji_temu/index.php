<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$sql = "SELECT jt.id_janji, p.nama_pasien, d.nama_dokter, d.spesialisasi,
               jt.tgl_janji, jt.keluhan, jt.status
        FROM janji_temu jt
        JOIN pasien p ON jt.id_pasien = p.id_pasien
        JOIN dokter d ON jt.id_dokter = d.id_dokter
        ORDER BY jt.tgl_janji DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="page-head">
  <h1>Data Janji Temu</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah Janji Temu</a>
</div>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan = [
        'tambah' => 'Janji temu berhasil ditambahkan.',
        'edit'   => 'Janji temu berhasil diperbarui.',
        'hapus'  => 'Janji temu berhasil dihapus.'
      ];
      echo $pesan[$_GET['sukses']] ?? 'Berhasil.';
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">Janji temu tidak dapat dihapus karena masih memiliki rekam medis atau pembayaran terkait.</div>
<?php endif; ?>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Pasien</th>
        <th>Dokter</th>
        <th>Spesialisasi</th>
        <th>Tanggal &amp; Jam</th>
        <th>Keluhan</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
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
            <td class="actions">
              <a href="edit.php?id=<?= $row['id_janji'] ?>" class="btn btn-edit btn-sm">Edit</a>
              <a href="hapus.php?id=<?= $row['id_janji'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin menghapus janji temu ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="empty-state">Belum ada data janji temu.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../../config/footer.php'; ?>
