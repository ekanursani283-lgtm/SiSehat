<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost = (int) $_POST['id_bayar'];
    $tgl    = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);
    $total  = (float) $_POST['total_biaya'];
    $metode = mysqli_real_escape_string($conn, $_POST['metode_bayar']);
    $status = mysqli_real_escape_string($conn, $_POST['status_bayar']);

    $stmt = mysqli_prepare($conn,
        "UPDATE pembayaran SET tgl_bayar = ?, total_biaya = ?, metode_bayar = ?, status_bayar = ? WHERE id_bayar = ?"
    );
    mysqli_stmt_bind_param($stmt, 'sdssi', $tgl, $total, $metode, $status, $idPost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=edit');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM pembayaran WHERE id_bayar = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$bayar = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$bayar) {
    echo '<div class="container"><div class="alert alert-danger">Data pembayaran tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}
?>

<div class="page-head">
  <h1>Edit Pembayaran</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_bayar" value="<?= $bayar['id_bayar'] ?>">
    <div class="form-group">
      <label>Tanggal Bayar</label>
      <input type="date" name="tgl_bayar" required value="<?= $bayar['tgl_bayar'] ?>">
    </div>
    <div class="form-group">
      <label>Total Biaya (Rp)</label>
      <input type="number" name="total_biaya" required min="0" step="1000" value="<?= $bayar['total_biaya'] ?>">
    </div>
    <div class="form-group">
      <label>Metode Pembayaran</label>
      <select name="metode_bayar" required>
        <option value="Tunai" <?= $bayar['metode_bayar'] === 'Tunai' ? 'selected' : '' ?>>Tunai</option>
        <option value="Transfer" <?= $bayar['metode_bayar'] === 'Transfer' ? 'selected' : '' ?>>Transfer</option>
        <option value="BPJS" <?= $bayar['metode_bayar'] === 'BPJS' ? 'selected' : '' ?>>BPJS</option>
      </select>
    </div>
    <div class="form-group">
      <label>Status Pembayaran</label>
      <select name="status_bayar" required>
        <option value="Belum Lunas" <?= $bayar['status_bayar'] === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
        <option value="Lunas" <?= $bayar['status_bayar'] === 'Lunas' ? 'selected' : '' ?>>Lunas</option>
      </select>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
