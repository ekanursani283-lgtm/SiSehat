<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_obat']);
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $harga  = (float) $_POST['harga'];
    $stok   = (int) $_POST['stok'];

    $stmt = mysqli_prepare($conn, "INSERT INTO obat (nama_obat, satuan, harga, stok) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssdi', $nama, $satuan, $harga, $stok);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=tambah');
    exit;
}
?>

<div class="page-head">
  <h1>Tambah Obat</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="tambah.php">
    <div class="form-group">
      <label>Nama Obat</label>
      <input type="text" name="nama_obat" required placeholder="contoh: Paracetamol 500mg">
    </div>
    <div class="form-group">
      <label>Satuan</label>
      <input type="text" name="satuan" required placeholder="contoh: Tablet, Kapsul, Botol">
    </div>
    <div class="form-group">
      <label>Harga (Rp)</label>
      <input type="number" name="harga" required min="0" step="100">
    </div>
    <div class="form-group">
      <label>Stok</label>
      <input type="number" name="stok" required min="0">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
