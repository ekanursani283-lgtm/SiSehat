<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id === (int) $_SESSION['id_user']) {
    header('Location: index.php?error=sendiri');
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: index.php?sukses=hapus');
exit;
