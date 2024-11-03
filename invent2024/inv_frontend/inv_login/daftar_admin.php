<?php 
// Mengaktifkan tampilan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pathInPieces = 'invent2024';  
require_once ($_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/akm_config/akm_config.php'); 
require_once ($_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/akm_config/akm_koneksi.php'); 

session_start(); 

// Mendapatkan hari ini dan waktu saat ini
$hari_ini = date('l');
$jam_sekarang = date('H:i:s');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Memeriksa apakah variabel POST diset
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
    $pwd_admin = isset($_POST['pwd_admin']) ? $_POST['pwd_admin'] : '';

    // Hash password sebelum menyimpannya
    $hashed_pwd = password_hash($pwd_admin, PASSWORD_DEFAULT);

    // Menyimpan data pengguna baru ke tabel admin
    $query = "INSERT INTO admin (nama, pwd_admin) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Persiapan query gagal: " . $conn->error);
    }
    $stmt->bind_param("ss", $nama, $hashed_pwd);

    if ($stmt->execute()) {
        // Redirect ke halaman login setelah pendaftaran sukses
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Pendaftaran gagal. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
</head>
<body class="bg03">
    <div class="container">
        <div class="row tm-mt-big">
            <div class="col-12 mx-auto tm-login-col">
                <div class="bg-white tm-block">
                    <div class="row">
                        <div class="col-12 text-center">
                            <i class="fas fa-3x fa-user-plus tm-site-icon text-center"></i>
                            <h2 class="tm-block-title mt-3">Daftar admin</h2>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <form action="" method="post" class="tm-login-form">
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                                <?php endif; ?>
                                <div class="input-group">
                                    <label for="username" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Username</label>
                                    <input name="nama" type="text" class="form-control validate col-xl-9 col-lg-8 col-md-8 col-sm-7" id="username" required>
                                </div>
                                <div class="input-group mt-3">
                                    <label for="password" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Password</label>
                                    <input name="pwd_admin" type="password" class="form-control validate" id="password" required>
                                </div>
                                <div class="input-group mt-3">
                                    <button type="submit" class="btn btn-primary d-inline-block mx-auto">Daftar</button>
                                </div>
                                <div class="input-group mt-3">
                                    <p><em>Sudah punya akun? <a href="?hal=login">Masuk di sini</a></em></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="row tm-mt-big">
            <div class="col-12 font-weight-light text-center">
                <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                    Copyright &copy; 2018. Created by
                    <a href="http://www.tooplate.com" class="text-white tm-footer-link">Tooplate</a> | Distributed by <a href="https://themewagon.com" class="text-white tm-footer-link">ThemeWagon</a>
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
