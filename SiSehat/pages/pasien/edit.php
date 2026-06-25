<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost  = (int) $_POST['id_pasien'];
    $nama    = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $tgl     = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jk      = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);

    $stmt = mysqli_prepare($conn,
        "UPDATE pasien
         SET nama_pasien = ?, tanggal_lahir = ?, jenis_kelamin = ?, alamat = ?, no_telepon = ?
         WHERE id_pasien = ?"
    );
    mysqli_stmt_bind_param($stmt, 'sssssi', $nama, $tgl, $jk, $alamat, $telepon, $idPost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=edit');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM pasien WHERE id_pasien = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$pasien = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pasien) {
    echo '<div class="container"><div class="alert alert-danger">Data pasien tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}
?>

<div class="page-head">
  <h1>Edit Pasien</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_pasien" value="<?= $pasien['id_pasien'] ?>">
    <div class="form-group">
      <label>Nama Pasien</label>
      <input type="text" name="nama_pasien" required value="<?= htmlspecialchars($pasien['nama_pasien']) ?>">
    </div>
    <div class="form-group">
      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" required value="<?= $pasien['tanggal_lahir'] ?>">
    </div>
    <div class="form-group">
      <label>Jenis Kelamin</label>
      <select name="jenis_kelamin" required>
        <option value="Laki-laki" <?= $pasien['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
        <option value="Perempuan" <?= $pasien['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
      </select>
    </div>
    <div class="form-group">
      <label>Alamat</label>
      <textarea name="alamat" rows="3"><?= htmlspecialchars($pasien['alamat']) ?></textarea>
    </div>
    <div class="form-group">
      <label>No. Telepon</label>
      <input type="text" name="no_telepon" value="<?= htmlspecialchars($pasien['no_telepon']) ?>">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
