<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$result = mysqli_query($conn, "SELECT * FROM pasien ORDER BY id_pasien DESC");
?>

<div class="page-head">
  <h1>Data Pasien</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah Pasien</a>
</div>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan = [
        'tambah' => 'Data pasien berhasil ditambahkan.',
        'edit'   => 'Data pasien berhasil diperbarui.',
        'hapus'  => 'Data pasien berhasil dihapus.'
      ];
      echo $pesan[$_GET['sukses']] ?? 'Berhasil.';
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?= $_GET['error'] === 'relasi' ? 'Pasien tidak dapat dihapus karena masih memiliki data janji temu.' : 'Terjadi kesalahan.' ?>
  </div>
<?php endif; ?>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Nama Pasien</th>
        <th>Tanggal Lahir</th>
        <th>Jenis Kelamin</th>
        <th>Alamat</th>
        <th>No. Telepon</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
            <td><?= date('d M Y', strtotime($row['tanggal_lahir'])) ?></td>
            <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
            <td><?= htmlspecialchars($row['alamat']) ?></td>
            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
            <td class="actions">
              <a href="edit.php?id=<?= $row['id_pasien'] ?>" class="btn btn-edit btn-sm">Edit</a>
              <a href="hapus.php?id=<?= $row['id_pasien'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin menghapus data pasien ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="empty-state">Belum ada data pasien.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../../config/footer.php'; ?>
