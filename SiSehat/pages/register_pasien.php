<?php
session_start();
require_once '../config/koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama            = mysqli_real_escape_string($conn, $_POST['nama']);
    $email           = mysqli_real_escape_string($conn, $_POST['email']);
    $tanggal_lahir   = $_POST['tanggal_lahir'];
    $jenis_kelamin   = $_POST['jenis_kelamin'];
    $alamat          = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon         = mysqli_real_escape_string($conn, $_POST['telepon']);
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){

        $error = "Email sudah terdaftar!";

    } else {

        $simpanPasien = mysqli_query(
            $conn,
            "INSERT INTO pasien
            (
                nama_pasien,
                tanggal_lahir,
                jenis_kelamin,
                alamat,
                no_telepon
            )
            VALUES
            (
                '$nama',
                '$tanggal_lahir',
                '$jenis_kelamin',
                '$alamat',
                '$telepon'
            )"
        );

        if($simpanPasien){

            $id_pasien = mysqli_insert_id($conn);

            mysqli_query(
                $conn,
                "INSERT INTO users
                (
                    username,
                    email,
                    password,
                    role,
                    id_pasien
                )
                VALUES
                (
                    '$email',
                    '$email',
                    '$password',
                    'Pasien',
                    '$id_pasien'
                )"
            );

            $success = "Pendaftaran berhasil!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Pasien - SiSehat</title>

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI,sans-serif;
}

body{
background:linear-gradient(135deg,#00A3A6,#007A7D);
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:20px;
}

.card{
background:#fff;
width:100%;
max-width:500px;
padding:35px;
border-radius:20px;
box-shadow:0 15px 40px rgba(0,0,0,.2);
}

h2{
text-align:center;
color:#00A3A6;
margin-bottom:25px;
}

.form-group{
margin-bottom:15px;
}

label{
display:block;
margin-bottom:6px;
font-weight:600;
}

input,
select,
textarea{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
}

textarea{
height:90px;
resize:none;
}

button{
width:100%;
padding:14px;
background:#00A3A6;
color:white;
border:none;
border-radius:50px;
font-size:16px;
font-weight:bold;
cursor:pointer;
}

button:hover{
background:#007A7D;
}

.error{
background:#ffdede;
color:#c62828;
padding:10px;
border-radius:8px;
margin-bottom:15px;
}

.success{
background:#dff5df;
color:#2e7d32;
padding:10px;
border-radius:8px;
margin-bottom:15px;
text-align:center;
}

.success a{
color:#2e7d32;
font-weight:bold;
}

.login-link{
text-align:center;
margin-top:15px;
}

.login-link a{
color:#00A3A6;
font-weight:bold;
text-decoration:none;
}
</style>
</head>
<body>

<div class="card">

<h2>🏥 Registrasi Pasien</h2>

<?php if($error): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="success">
    <?= $success ?>
    <br><br>
    <a href="../login.php">Klik di sini untuk Login →</a>
</div>
<?php endif; ?>

<?php if(!$success): ?>
<form method="POST">

<div class="form-group">
<label>Nama Lengkap</label>
<input type="text" name="nama" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="form-group">
<label>Tanggal Lahir</label>
<input type="date" name="tanggal_lahir" required>
</div>

<div class="form-group">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" required>
<option value="">Pilih</option>
<option value="Laki-laki">Laki-laki</option>
<option value="Perempuan">Perempuan</option>
</select>
</div>

<div class="form-group">
<label>No Telepon</label>
<input type="text" name="telepon" required>
</div>

<div class="form-group">
<label>Alamat</label>
<textarea name="alamat" required></textarea>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<button type="submit">
Daftar Sekarang
</button>

</form>

<a href="../login.php">Klik di sini untuk Login →</a>
<div class="login-link">
    Sudah punya akun?
    <a href="../login.php">Login</a>
</div>

<?php endif; ?>

</div>

</body>
</html>