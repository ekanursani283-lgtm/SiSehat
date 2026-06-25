<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$sql = "SELECT pb.id_bayar, pb.tgl_bayar, pb.total_biaya, pb.metode_bayar, pb.status_bayar,
               p.nama_pasien, d.nama_dokter
        FROM pembayaran pb
        JOIN janji_temu jt ON pb.id_janji = jt.id_janji
        JOIN pasien p ON jt.id_pasien = p.id_pasien
        JOIN dokter d ON jt.id_dokter = d.id_dokter
        ORDER BY pb.tgl_bayar DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="page-head">
  <h1>Data Pembayaran</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah Pembayaran</a>
</div>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan = [
        'tambah' => 'Pembayaran berhasil ditambahkan.',
        'edit'   => 'Pembayaran berhasil diperbarui.',
        'hapus'  => 'Pembayaran berhasil dihapus.'
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
        <th>Tgl Bayar</th>
        <th>Total</th>
        <th>Metode</th>
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
            <td><?= date('d M Y', strtotime($row['tgl_bayar'])) ?></td>
            <td>Rp <?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($row['metode_bayar']) ?></td>
            <td>
              <span class="badge <?= $row['status_bayar'] === 'Lunas' ? 'badge-lunas' : 'badge-belum' ?>">
                <?= $row['status_bayar'] ?>
              </span>
            </td>
            <td class="actions">
              <a href="edit.php?id=<?= $row['id_bayar'] ?>" class="btn btn-edit btn-sm">Edit</a>
              <a href="hapus.php?id=<?= $row['id_bayar'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin menghapus data pembayaran ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="empty-state">Belum ada data pembayaran.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../../config/footer.php'; ?>
