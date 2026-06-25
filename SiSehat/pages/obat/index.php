<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$result = mysqli_query($conn, "SELECT * FROM obat ORDER BY id_obat DESC");
?>

<div class="page-head">
  <h1>Data Obat</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah Obat</a>
</div>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan = [
        'tambah' => 'Data obat berhasil ditambahkan.',
        'edit'   => 'Data obat berhasil diperbarui.',
        'hapus'  => 'Data obat berhasil dihapus.'
      ];
      echo $pesan[$_GET['sukses']] ?? 'Berhasil.';
    ?>
  </div>
<?php endif; ?>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Nama Obat</th>
        <th>Satuan</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_obat']) ?></td>
            <td><?= htmlspecialchars($row['satuan']) ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td><?= $row['stok'] ?></td>
            <td class="actions">
              <a href="edit.php?id=<?= $row['id_obat'] ?>" class="btn btn-edit btn-sm">Edit</a>
              <a href="hapus.php?id=<?= $row['id_obat'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin menghapus data obat ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="empty-state">Belum ada data obat.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../../config/footer.php'; ?>
