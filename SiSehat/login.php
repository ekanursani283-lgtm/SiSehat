<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['id_user'])) {
    header('Location: ' . ($_SESSION['role'] === 'Admin'
        ? 'pages/admin_dashboard.php'
        : 'pages/dashboard_pasien.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login    = trim($_POST['login']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users
         WHERE username = ?
         OR email = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, 'ss', $login, $login);
    mysqli_stmt_execute($stmt);

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['id_user']   = $user['id_user'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['id_pasien'] = $user['id_pasien'];

        header('Location: ' .
            ($user['role'] === 'Admin'
                ? 'pages/admin_dashboard.php'
                : 'pages/dashboard_pasien.php'));
        exit;
    }

    $error = 'Email/Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login - SiSehat</title>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Inter',sans-serif;
    background:linear-gradient(135deg,#00A3A6,#007A7D);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.wrap{
    width:100%;
    max-width:420px;
    padding:20px;
}

.card{
    background:#fff;
    border-radius:24px;
    padding:40px 36px;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
}

.logo{
    text-align:center;
    font-size:34px;
    font-weight:800;
    color:#00A3A6;
    font-family:'Montserrat',sans-serif;
    margin-bottom:8px;
}

.sub{
    text-align:center;
    color:#4B6664;
    margin-bottom:28px;
}

.fg{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#14302F;
}

input{
    width:100%;
    padding:13px 15px;
    border:1.5px solid #d0e8e8;
    border-radius:12px;
    font-size:14px;
}

input:focus{
    outline:none;
    border-color:#00A3A6;
    box-shadow:0 0 0 4px rgba(0,163,166,.1);
}

.btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:999px;
    background:#00A3A6;
    color:#fff;
    font-weight:700;
    cursor:pointer;
    font-size:15px;
    margin-top:10px;
}

.btn:hover{
    background:#007A7D;
}

.err{
    background:#FBE2E0;
    color:#B1372D;
    padding:12px;
    border-radius:10px;
    margin-bottom:18px;
}

.register-link{
    text-align:center;
    margin-top:20px;
    font-size:14px;
    color:#4B6664;
}

.register-link a{
    color:#00A3A6;
    font-weight:700;
    text-decoration:none;
}

.register-link a:hover{
    text-decoration:underline;
}
</style>
</head>

<body>

<div class="wrap">
    <div class="card">

        <div class="logo">🏥 SiSehat</div>

        <div class="sub">
            Sistem Informasi Klinik Sederhana
        </div>

        <?php if($error): ?>
            <div class="err">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="fg">
                <label>Email atau Username</label>
                <input
                    type="text"
                    name="login"
                    placeholder="Masukkan email atau username"
                    required
                    autofocus>
            </div>

            <div class="fg">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required>
            </div>

            <button type="submit" class="btn">
                Masuk
            </button>

        </form>

        <div class="register-link">
    Belum punya akun? 
    <a href="pages/register_pasien.php">Daftar di sini</a>
</div>

    </div>
</div>

</body>
</html>