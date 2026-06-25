<?php
// =============================================
// KONEKSI DATABASE — SiSehat
// =============================================

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sisehat';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('<h3 style="color:red">Koneksi database gagal: ' . mysqli_connect_error() . '</h3>');
}

mysqli_set_charset($conn, 'utf8mb4');
?>