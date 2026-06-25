<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $idPasien = $role === 'Pasien' && !empty($_POST['id_pasien']) ? (int) $_POST['id_pasien'] : null;

    $cek = mysqli_query($conn, "SELECT id_user FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Username sudah digunakan, silakan pilih username lain.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn,
            "INSERT INTO users (username, password, role, id_pasien) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'sssi', $username, $hash, $role, $idPasien);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header('Location: index.php?sukses=tambah');
        exit;
    }
}

$daftarPasien = mysqli_query($conn,
    "SELECT p.id_pasien, p.nama_pasien FROM pasien p
     WHERE p.id_pasien NOT IN (SELECT id_pasien FROM users WHERE id_pasien IS NOT NULL)
     ORDER BY p.nama_pasien"
);
?>

<div class="page-head">
  <h1>Tambah User</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="POST" action="tambah.php">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" required placeholder="Masukkan username">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required placeholder="Minimal 6 karakter" minlength="6">
    </div>
    <div class="form-group">
      <label>Role</label>
      <select name="role" id="roleSelect" required onchange="toggleFieldPasien()">
        <option value="Admin">Admin</option>
        <option value="Pasien">Pasien</option>
      </select>
    </div>
    <div class="form-group" id="fieldPasien" style="display:none;">
      <label>Hubungkan ke Data Pasien</label>
      <select name="id_pasien">
        <option value="">-- Pilih Pasien --</option>
        <?php while ($p = mysqli_fetch_assoc($daftarPasien)): ?>
          <option value="<?= $p['id_pasien'] ?>"><?= htmlspecialchars($p['nama_pasien']) ?></option>
        <?php endwhile; ?>
      </select>
      <small style="color:#6b7280;">Hanya pasien yang belum memiliki akun yang ditampilkan.</small>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

<script>
function toggleFieldPasien() {
  const role = document.getElementById('roleSelect').value;
  document.getElementById('fieldPasien').style.display = role === 'Pasien' ? 'block' : 'none';
}
</script>

<?php include '../../config/footer.php'; ?>
