<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJanji   = (int) $_POST['id_janji'];
    $diagnosa  = mysqli_real_escape_string($conn, $_POST['diagnosa']);
    $resep     = mysqli_real_escape_string($conn, $_POST['resep_obat']);
    $catatan   = mysqli_real_escape_string($conn, $_POST['catatan']);
    $tglPeriksa = mysqli_real_escape_string($conn, $_POST['tgl_periksa']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO rekam_medis (id_janji, diagnosa, resep_obat, catatan, tgl_periksa) VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'issss', $idJanji, $diagnosa, $resep, $catatan, $tglPeriksa);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php?sukses=tambah');
    exit;
}

$sqlJanji = "SELECT jt.id_janji, p.nama_pasien, d.nama_dokter, jt.tgl_janji
             FROM janji_temu jt
             JOIN pasien p ON jt.id_pasien = p.id_pasien
             JOIN dokter d ON jt.id_dokter = d.id_dokter
             WHERE jt.status = 'Selesai'
             ORDER BY jt.tgl_janji DESC";
$daftarJanji = mysqli_query($conn, $sqlJanji);
?>

<div class="page-head">
  <h1>Tambah Rekam Medis</h1>
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
      <small style="color:#6b7280;">Hanya menampilkan janji temu berstatus "Selesai".</small>
    </div>
    <div class="form-group">
      <label>Diagnosa</label>
      <textarea name="diagnosa" rows="2" required placeholder="Diagnosa dokter"></textarea>
    </div>
    <div class="form-group">
      <label>Resep Obat</label>
      <textarea name="resep_obat" rows="2" placeholder="Daftar obat yang diresepkan"></textarea>
    </div>
    <div class="form-group">
      <label>Catatan</label>
      <textarea name="catatan" rows="2" placeholder="Catatan tambahan"></textarea>
    </div>
    <div class="form-group">
      <label>Tanggal Periksa</label>
      <input type="date" name="tgl_periksa" required>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<?php include '../../config/footer.php'; ?>
