<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost   = (int) $_POST['id_janji'];
    $idPasien = (int) $_POST['id_pasien'];
    $idDokter = (int) $_POST['id_dokter'];
    $tgl      = mysqli_real_escape_string($conn, $_POST['tgl_janji']);
    $keluhan  = mysqli_real_escape_string($conn, $_POST['keluhan']);
    $status   = mysqli_real_escape_string($conn, $_POST['status']);

    $stmt = mysqli_prepare($conn,
        "UPDATE janji_temu SET id_pasien = ?, id_dokter = ?, tgl_janji = ?, keluhan = ?, status = ? WHERE id_janji = ?"
    );
    mysqli_stmt_bind_param($stmt, 'iisssi', $idPasien, $idDokter, $tgl, $keluhan, $status, $idPost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=edit');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM janji_temu WHERE id_janji = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$janji = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$janji) {
    echo '<div class="container"><div class="alert alert-danger">Data janji temu tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}

$daftarPasien = mysqli_query($conn, "SELECT id_pasien, nama_pasien FROM pasien ORDER BY nama_pasien");
$daftarDokter = mysqli_query($conn, "SELECT id_dokter, nama_dokter, spesialisasi FROM dokter ORDER BY nama_dokter");
$tglValue = date('Y-m-d\TH:i', strtotime($janji['tgl_janji']));
?>

<div class="page-head">
  <h1>Edit Janji Temu</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_janji" value="<?= $janji['id_janji'] ?>">
    <div class="form-group">
      <label>Pasien</label>
      <select name="id_pasien" required>
        <?php while ($p = mysqli_fetch_assoc($daftarPasien)): ?>
          <option value="<?= $p['id_pasien'] ?>" <?= $p['id_pasien'] == $janji['id_pasien'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['nama_pasien']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Dokter</label>
      <select name="id_dokter" required>
        <?php while ($d = mysqli_fetch_assoc($daftarDokter)): ?>
          <option value="<?= $d['id_dokter'] ?>" <?= $d['id_dokter'] == $janji['id_dokter'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['nama_dokter']) ?> (<?= htmlspecialchars($d['spesialisasi']) ?>)
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Tanggal &amp; Jam</label>
      <input type="datetime-local" name="tgl_janji" required value="<?= $tglValue ?>">
    </div>
    <div class="form-group">
      <label>Keluhan</label>
      <textarea name="keluhan" rows="3"><?= htmlspecialchars($janji['keluhan']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Status</label>
      <select name="status" required>
        <option value="Menunggu" <?= $janji['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
        <option value="Selesai" <?= $janji['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        <option value="Batal" <?= $janji['status'] === 'Batal' ? 'selected' : '' ?>>Batal</option>
      </select>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
