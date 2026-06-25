<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $tgl     = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jk      = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO pasien (nama_pasien, tanggal_lahir, jenis_kelamin, alamat, no_telepon)
         VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssss', $nama, $tgl, $jk, $alamat, $telepon);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=tambah');
    exit;
}
?>

<div class="page-head">
  <h1>Tambah Pasien</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="tambah.php">
    <div class="form-group">
      <label>Nama Pasien</label>
      <input type="text" name="nama_pasien" required placeholder="Masukkan nama lengkap pasien">
    </div>
    <div class="form-group">
      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" required>
    </div>
    <div class="form-group">
      <label>Jenis Kelamin</label>
      <select name="jenis_kelamin" required>
        <option value="">-- Pilih --</option>
        <option value="Laki-laki">Laki-laki</option>
        <option value="Perempuan">Perempuan</option>
      </select>
    </div>
    <div class="form-group">
      <label>Alamat</label>
      <textarea name="alamat" rows="3" placeholder="Alamat lengkap"></textarea>
    </div>
    <div class="form-group">
      <label>No. Telepon</label>
      <input type="text" name="no_telepon" placeholder="08xxxxxxxxxx">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
