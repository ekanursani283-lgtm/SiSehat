<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $sp   = mysqli_real_escape_string($conn, $_POST['spesialisasi']);
    $hp   = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $hariPraktik = isset($_POST['hari_praktik']) ? implode(',', $_POST['hari_praktik']) : '';

    $stmt = mysqli_prepare($conn, "INSERT INTO dokter (nama_dokter, spesialisasi, no_hp, hari_praktik) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $nama, $sp, $hp, $hariPraktik);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=tambah');
    exit;
}
?>

<div class="page-head">
  <h1>Tambah Dokter</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="tambah.php">
    <div class="form-group">
      <label>Nama Dokter</label>
      <input type="text" name="nama_dokter" required placeholder="contoh: dr. Ahmad Fauzi">
    </div>
    <div class="form-group">
      <label>Spesialisasi</label>
      <input type="text" name="spesialisasi" required placeholder="contoh: Umum, Spesialis Anak">
    </div>
    <div class="form-group">
      <label>No. HP</label>
      <input type="text" name="no_hp" placeholder="08xxxxxxxxxx">
    </div>
    <div class="form-group">
      <label>Hari Praktik</label>
      <div style="display:flex; flex-wrap:wrap; gap:10px;">
        <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari): ?>
          <label style="display:flex; align-items:center; gap:5px; font-weight:400;">
            <input type="checkbox" name="hari_praktik[]" value="<?= $hari ?>"> <?= $hari ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>