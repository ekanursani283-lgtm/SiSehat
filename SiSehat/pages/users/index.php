<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$sql = "SELECT u.id_user, u.username, u.role, u.id_pasien, p.nama_pasien
        FROM users u
        LEFT JOIN pasien p ON u.id_pasien = p.id_pasien
        ORDER BY u.id_user DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="page-head">
  <h1>Kelola User</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah User</a>
</div>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan = [
        'tambah' => 'Akun user berhasil ditambahkan.',
        'edit'   => 'Akun user berhasil diperbarui.',
        'hapus'  => 'Akun user berhasil dihapus.'
      ];
      echo $pesan[$_GET['sukses']] ?? 'Berhasil.';
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?= $_GET['error'] === 'sendiri' ? 'Anda tidak dapat menghapus akun yang sedang digunakan.' : 'Terjadi kesalahan.' ?>
  </div>
<?php endif; ?>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Terhubung ke Pasien</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td>
              <span class="badge <?= $row['role'] === 'Admin' ? 'badge-selesai' : 'badge-menunggu' ?>">
                <?= $row['role'] ?>
              </span>
            </td>
            <td><?= $row['nama_pasien'] ? htmlspecialchars($row['nama_pasien']) : '-' ?></td>
            <td class="actions">
              <a href="edit.php?id=<?= $row['id_user'] ?>" class="btn btn-edit btn-sm">Edit</a>
              <?php if ($row['id_user'] != $_SESSION['id_user']): ?>
                <a href="hapus.php?id=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="empty-state">Belum ada akun user.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../../config/footer.php'; ?>
