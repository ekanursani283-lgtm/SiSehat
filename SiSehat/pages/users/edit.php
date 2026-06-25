<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$id = (int) ($_GET['id'] ?? 0);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost   = (int) $_POST['id_user'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $idPasien = $role === 'Pasien' && !empty($_POST['id_pasien']) ? (int) $_POST['id_pasien'] : null;

    $cek = mysqli_query($conn, "SELECT id_user FROM users WHERE username = '$username' AND id_user != $idPost");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Username sudah digunakan oleh akun lain.';
    } else {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn,
                "UPDATE users SET username = ?, password = ?, role = ?, id_pasien = ? WHERE id_user = ?"
            );
            mysqli_stmt_bind_param($stmt, 'sssii', $username, $hash, $role, $idPasien, $idPost);
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE users SET username = ?, role = ?, id_pasien = ? WHERE id_user = ?"
            );
            mysqli_stmt_bind_param($stmt, 'ssii', $username, $role, $idPasien, $idPost);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header('Location: index.php?sukses=edit');
        exit;
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    echo '<div class="container"><div class="alert alert-danger">Akun user tidak ditemukan.</div></div>';
    include '../../config/footer.php';
    exit;
}

$daftarPasien = mysqli_query($conn,
    "SELECT p.id_pasien, p.nama_pasien FROM pasien p
     WHERE p.id_pasien NOT IN (SELECT id_pasien FROM users WHERE id_pasien IS NOT NULL AND id_user != $id)
     ORDER BY p.nama_pasien"
);
?>

<div class="page-head">
  <h1>Edit User</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>">
    </div>
    <div class="form-group">
      <label>Password Baru</label>
      <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
    </div>
    <div class="form-group">
      <label>Role</label>
      <select name="role" id="roleSelect" required onchange="toggleFieldPasien()">
        <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="Pasien" <?= $user['role'] === 'Pasien' ? 'selected' : '' ?>>Pasien</option>
      </select>
    </div>
    <div class="form-group" id="fieldPasien" style="<?= $user['role'] === 'Pasien' ? '' : 'display:none;' ?>">
      <label>Hubungkan ke Data Pasien</label>
      <select name="id_pasien">
        <option value="">-- Pilih Pasien --</option>
        <?php while ($p = mysqli_fetch_assoc($daftarPasien)): ?>
          <option value="<?= $p['id_pasien'] ?>" <?= $p['id_pasien'] == $user['id_pasien'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['nama_pasien']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
