<<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip  = (int) $_POST['id_pasien'];
    $id  = (int) $_POST['id_dokter'];
    $tgl = $_POST['tgl_janji'];
    $kel = $_POST['keluhan'];
    $sts = 'Menunggu';
    $stmt = mysqli_prepare($conn, "INSERT INTO janji_temu(id_pasien,id_dokter,tgl_janji,keluhan,status) VALUES(?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iisss', $ip, $id, $tgl, $kel, $sts);
    mysqli_stmt_execute($stmt);
    header('Location: index.php?s=ditambahkan');
    exit;
}

$daftarPasien = mysqli_query($conn, "SELECT id_pasien,nama_pasien FROM pasien ORDER BY nama_pasien");
$daftarDokter = mysqli_query($conn, "SELECT id_dokter,nama_dokter,spesialisasi FROM dokter ORDER BY nama_dokter");

include '../../config/header.php';
include 'tambah_view.php';
include '../../config/footer.php';
?>