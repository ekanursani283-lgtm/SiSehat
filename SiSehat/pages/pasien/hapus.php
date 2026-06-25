<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "DELETE FROM pasien WHERE id_pasien = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: index.php?sukses=hapus');
} else {
    // Gagal hapus karena masih ada relasi (foreign key) di tabel janji_temu
    header('Location: index.php?error=relasi');
}
mysqli_stmt_close($stmt);
exit;
