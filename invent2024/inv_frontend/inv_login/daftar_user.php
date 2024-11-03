<?php 
// Mengaktifkan tampilan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mengimpor koneksi database
include 'db.php'; 
session_start(); 

// Memuat library PHP QR Code
require_once 'phpqrcode/qrlib.php'; // Sesuaikan path ke qrlib.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Memeriksa apakah variabel POST diset
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? ''; // Ambil nomor HP
    $passwd = $_POST['passwd'] ?? '';

    // Validasi nomor HP
    if (!preg_match('/^(0|(\+62))[1-9][0-9]{9,12}$/', $no_hp)) {
        $error_message = "Nomor HP tidak valid. Harus diawali dengan 0 atau +62, dan diikuti oleh 9-12 digit.";
    } else {
        // Tetapkan id_jabatan secara otomatis (misal: 3)
        $id_jabatan = 3; // Ganti dengan id_jabatan yang sesuai

        // Hash password sebelum menyimpannya
        $hashed_pwd = password_hash($passwd, PASSWORD_DEFAULT);

        // Buat direktori untuk menyimpan QR Code jika belum ada
        $qrCodeDir = $_SERVER['DOCUMENT_ROOT'] . '/inv_upload/uploads/qrcode_user';
        if (!is_dir($qrCodeDir)) {
            mkdir($qrCodeDir, 0755, true);
        }

        // Generate QR Code
        $dataForQRCode = "Nama: $nama, Email: $email, ID Jabatan: $id_jabatan, No HP: $no_hp"; // Gabungkan data
        $qrCodeFilePath = $qrCodeDir . '/' . uniqid() . '.png'; // Menggunakan uniqid untuk nama file QR
        Rcode::png($dataForQRCode, $qrCodeFilePath, QR_ECLEVEL_L, 4); // Generate QR Code dengan data yang telah disusun

        // Simpan path QR Code ke dalam database
        $qrCodePath = 'inv_upload/uploads/qrcode_user/' . basename($qrCodeFilePath); // Simpan path relatif

        // Menyimpan data pengguna baru ke tabel users
        $queryUser = "INSERT INTO users (nama, email, id_jabatan, passwd, qrcode, no_hp) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtUser = $conn->prepare($queryUser);
        
        if (!$stmtUser) {
            die("Persiapan query gagal untuk users: " . $conn->error);
        }

        // Bind parameter, termasuk hashed password
        $stmtUser->bind_param("ssisss", $nama, $email, $id_jabatan, $hashed_pwd, $qrCodePath, $no_hp);

        if ($stmtUser->execute()) {
            // Redirect ke halaman login setelah pendaftaran sukses
            header("Location: ?hal=login");
            exit();
        } else {
            $error_message = "Pendaftaran gagal. Silakan coba lagi. " . $stmtUser->error;
        }
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
                            <h2 class="tm-block-title mt-3">Daftar User</h2>
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
                                    <label for="email" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Email Gmail</label>
                                    <input name="email" type="email" class="form-control validate col-xl-9 col-lg-8 col-md-8 col-sm-7" id="email" required>
                                </div>
                                <div class="input-group mt-3">
                                    <label for="no_hp" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Nomor HP</label>
                                    <input name="no_hp" type="text" class="form-control validate col-xl-9 col-lg-8 col-md-8 col-sm-7" id="no_hp" required pattern="^(0|(\+62))[1-9][0-9]{9,12}$" title="Nomor HP harus diawali dengan 0 atau +62, dan diikuti oleh 9-12 digit.">
                                </div>
                                <div class="input-group mt-3">
                                    <label for="password" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Password</label>
                                    <input name="passwd" type="password" class="form-control validate" id="password" required>
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
