<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost = (int) $_POST['id_obat'];
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_obat']);
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $harga  = (float) $_POST['harga'];
    $stok   = (int) $_POST['stok'];

    $stmt = mysqli_prepare($conn, "UPDATE obat SET nama_obat = ?, satuan = ?, harga = ?, stok = ? WHERE id_obat = ?");
    mysqli_stmt_bind_param($stmt, 'ssdii', $nama, $satuan, $harga, $stok, $idPost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=edit');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM obat WHERE id_obat = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$obat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$obat) {
    echo '<div class="container"><div class="alert alert-danger">Data obat tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}
?>

<div class="page-head">
  <h1>Edit Obat</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_obat" value="<?= $obat['id_obat'] ?>">
    <div class="form-group">
      <label>Nama Obat</label>
      <input type="text" name="nama_obat" required value="<?= htmlspecialchars($obat['nama_obat']) ?>">
    </div>
    <div class="form-group">
      <label>Satuan</label>
      <input type="text" name="satuan" required value="<?= htmlspecialchars($obat['satuan']) ?>">
    </div>
    <div class="form-group">
      <label>Harga (Rp)</label>
      <input type="number" name="harga" required min="0" step="100" value="<?= $obat['harga'] ?>">
    </div>
    <div class="form-group">
      <label>Stok</label>
      <input type="number" name="stok" required min="0" value="<?= $obat['stok'] ?>">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
