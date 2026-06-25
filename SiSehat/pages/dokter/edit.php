<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost = (int) $_POST['id_dokter'];
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $sp     = mysqli_real_escape_string($conn, $_POST['spesialisasi']);
    $hp     = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $hariPraktik = isset($_POST['hari_praktik']) ? implode(',', $_POST['hari_praktik']) : '';

    $stmt = mysqli_prepare($conn, "UPDATE dokter SET nama_dokter = ?, spesialisasi = ?, no_hp = ?, hari_praktik = ? WHERE id_dokter = ?");
    mysqli_stmt_bind_param($stmt, 'ssssi', $nama, $sp, $hp, $hariPraktik, $idPost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=edit');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM dokter WHERE id_dokter = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$dokter = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$dokter) {
    echo '<div class="container"><div class="alert alert-danger">Data dokter tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}

$hariTersimpan = explode(',', $dokter['hari_praktik']);
?>

<div class="page-head">
  <h1>Edit Dokter</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_dokter" value="<?= $dokter['id_dokter'] ?>">
    <div class="form-group">
      <label>Nama Dokter</label>
      <input type="text" name="nama_dokter" required value="<?= htmlspecialchars($dokter['nama_dokter']) ?>">
    </div>
    <div class="form-group">
      <label>Spesialisasi</label>
      <input type="text" name="spesialisasi" required value="<?= htmlspecialchars($dokter['spesialisasi']) ?>">
    </div>
    <div class="form-group">
      <label>No. HP</label>
      <input type="text" name="no_hp" value="<?= htmlspecialchars($dokter['no_hp']) ?>">
    </div>
    <div class="form-group">
      <label>Hari Praktik</label>
      <div style="display:flex; flex-wrap:wrap; gap:10px;">
        <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari): ?>
          <label style="display:flex; align-items:center; gap:5px; font-weight:400;">
            <input type="checkbox" name="hari_praktik[]" value="<?= $hari ?>" <?= in_array($hari, $hariTersimpan) ? 'checked' : '' ?>> <?= $hari ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>