<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJanji = (int) $_POST['id_janji'];
    $tgl     = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);
    $total   = (float) $_POST['total_biaya'];
    $metode  = mysqli_real_escape_string($conn, $_POST['metode_bayar']);
    $status  = mysqli_real_escape_string($conn, $_POST['status_bayar']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO pembayaran (id_janji, tgl_bayar, total_biaya, metode_bayar, status_bayar) VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'isdss', $idJanji, $tgl, $total, $metode, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=tambah');
    exit;
}

$sqlJanji = "SELECT jt.id_janji, p.nama_pasien, d.nama_dokter, jt.tgl_janji
             FROM janji_temu jt
             JOIN pasien p ON jt.id_pasien = p.id_pasien
             JOIN dokter d ON jt.id_dokter = d.id_dokter
             ORDER BY jt.tgl_janji DESC";
$daftarJanji = mysqli_query($conn, $sqlJanji);
?>

<div class="page-head">
  <h1>Tambah Pembayaran</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<div class="card">
  <form method="POST" action="tambah.php">
    <div class="form-group">
      <label>Janji Temu (Pasien - Dokter - Tanggal)</label>
      <select name="id_janji" required>
        <option value="">-- Pilih Janji Temu --</option>
        <?php while ($j = mysqli_fetch_assoc($daftarJanji)): ?>
          <option value="<?= $j['id_janji'] ?>">
            <?= htmlspecialchars($j['nama_pasien']) ?> - <?= htmlspecialchars($j['nama_dokter']) ?> - <?= date('d M Y', strtotime($j['tgl_janji'])) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Tanggal Bayar</label>
      <input type="date" name="tgl_bayar" required>
    </div>
    <div class="form-group">
      <label>Total Biaya (Rp)</label>
      <input type="number" name="total_biaya" required min="0" step="1000">
    </div>
    <div class="form-group">
      <label>Metode Pembayaran</label>
      <select name="metode_bayar" required>
        <option value="Tunai">Tunai</option>
        <option value="Transfer">Transfer</option>
        <option value="BPJS">BPJS</option>
      </select>
    </div>
    <div class="form-group">
      <label>Status Pembayaran</label>
      <select name="status_bayar" required>
        <option value="Belum Lunas">Belum Lunas</option>
        <option value="Lunas">Lunas</option>
      </select>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
