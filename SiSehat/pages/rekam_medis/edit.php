<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost     = (int) $_POST['id_rekam'];
    $diagnosa   = mysqli_real_escape_string($conn, $_POST['diagnosa']);
    $resep      = mysqli_real_escape_string($conn, $_POST['resep_obat']);
    $catatan    = mysqli_real_escape_string($conn, $_POST['catatan']);
    $tglPeriksa = mysqli_real_escape_string($conn, $_POST['tgl_periksa']);

    $stmt = mysqli_prepare($conn,
        "UPDATE rekam_medis SET diagnosa = ?, resep_obat = ?, catatan = ?, tgl_periksa = ? WHERE id_rekam = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $diagnosa, $resep, $catatan, $tglPeriksa, $idPost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=edit');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM rekam_medis WHERE id_rekam = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$rekam = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$rekam) {
    echo '<div class="container"><div class="alert alert-danger">Data rekam medis tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}
?>

<div class="page-head">
  <h1>Edit Rekam Medis</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_rekam" value="<?= $rekam['id_rekam'] ?>">
    <div class="form-group">
      <label>Diagnosa</label>
      <textarea name="diagnosa" rows="2" required><?= htmlspecialchars($rekam['diagnosa']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Resep Obat</label>
      <textarea name="resep_obat" rows="2"><?= htmlspecialchars($rekam['resep_obat']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Catatan</label>
      <textarea name="catatan" rows="2"><?= htmlspecialchars($rekam['catatan']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Tanggal Periksa</label>
      <input type="date" name="tgl_periksa" required value="<?= $rekam['tgl_periksa'] ?>">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
