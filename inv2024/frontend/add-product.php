<?php
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generateKode($conn) {
    $prefix = 'PAS';
    $query = "SELECT COUNT(*) as count FROM barang";
    $result = $conn->query($query);
    $data = $result->fetch_assoc();
    $count = $data['count'] + 1;
    return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
}

function generateKodeBarcode() {
    return 'PAS' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi dan ambil data dari form
    $kode = generateKode($conn);
    $nama_barang = $_POST['nama_barang'];
    $jumlah_barang = $_POST['jumlah_barang'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $foto = $_FILES['foto'];
    $kode_barcode = generateKodeBarcode();

    // Validasi ekstensi file
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "<script>alert('Format file tidak diizinkan.');</script>";
        return;
    }

    // Validasi ukuran file (misalnya maksimum 2MB)
    if ($foto['size'] > 9 * 1024 * 1024) {
        echo "<script>alert('File terlalu besar. Maksimum ukuran 2MB.');</script>";
        return;
    }

    // Jalur direktori target
    $target_dir = "/var/www/html/inv_upload/uploads/img/";
    $target_file = $target_dir . basename($foto['name']);

    // Cek dan buat direktori jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Cek error saat upload
    if ($foto['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Upload error: " . $foto['error'] . "');</script>";
        return;
    }

    // Pindahkan file yang di-upload
    if (move_uploaded_file($foto["tmp_name"], $target_file)) {
        // Simpan ke database
        $query = "INSERT INTO barang (kode_barang, nama_barang, jumlah_barang, foto, kode_barcode, tanggal_masuk)
                  VALUES ('$kode', '$nama_barang', '$jumlah_barang', '$target_file', '$kode_barcode', '$tanggal_masuk')";

        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Barang berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Product PAS</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="jquery-ui-datepicker/jquery-ui.min.css" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background */
        }
        .form-container {
            max-width: 500px; /* Set max width for the form */
            width: 100%; /* Responsive width */
            padding: 20px; /* Add some padding */
            border: 1px solid #ddd; /* Optional border */
            border-radius: 8px; /* Rounded corners */
            background-color: #fff; /* Background color */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            margin: auto; /* Center horizontally */
        }
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
        }
        .text-center {
            text-align: center; /* Center text */
        }
    </style>
</head>

<body id="reportsPage" class="bg02">
    <div id="home">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-xl navbar-light bg-light">
                        <a class="navbar-brand" href="index.html">
                            <i class="fas fa-3x fa-tachometer-alt tm-site-icon"></i>
                            <h1 class="tm-site-title mb-0">Dashboard</h1>
                        </a>
                        <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.html">Dashboard</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">Product</a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="?hal=produk">List Barang</a>
                                        <a class="dropdown-item" href="?hal=pinjam">Peminjaman</a>
                                        <a class="dropdown-item" href="?hal=kembali">Pengembalian</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="accounts.html">Accounts</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">Settings</a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="#">Profile</a>
                                        <a class="dropdown-item" href="#">Billing</a>
                                        <a class="dropdown-item" href="#">Customize</a>
                                    </div>
                                </li>
                            </ul>
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link d-flex" href="?hal=logout">
                                        <i class="far fa-user mr-2 tm-logout-icon"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>

        <div class="row tm-mt-big main-content">
            <div class="col-xl-8 col-lg-10 col-md-12 col-sm-12">
                <div class="bg-white tm-block">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="tm-block-title">Tambah Barang</h2>
                        </div>
                    </div>
                    <div class="row mt-4 tm-edit-product-row">
                        <div class="col-xl-12">
                            <div class="form-container">
                                <form action="" method="POST" enctype="multipart/form-data" class="tm-edit-product-form">
                                    <div class="form-group mb-3">
                                        <label for="kode" class="col-form-label">Kode Barang:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                            </div>
                                            <input type="text" id="kode" name="kode" value="<?= isset($kode) ? $kode : '' ?>" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kode_barcode" class="col-form-label">Kode Barcode:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                            </div>
                                            <input type="text" id="kode_barcode" name="kode_barcode" value="<?= isset($kode_barcode) ? $kode_barcode : '' ?>" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_barang" class="col-form-label">Nama Barang:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                            </div>
                                            <input type="text" id="nama_barang" name="nama_barang" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="jumlah_barang" class="col-form-label">Jumlah Barang:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                            </div>
                                            <input type="number" id="jumlah_barang" name="jumlah_barang" class="form-control" required min="1">
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tanggal_masuk" class="col-form-label">Tanggal Masuk:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" id="tanggal_masuk" name="tanggal_masuk" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="foto" class="col-form-label">Foto Barang:</label>
                                        <input type="file" id="foto" name="foto" accept="image/*" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">Tambah Barang</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="row tm-mt-big">
            <div class="col-12 font-weight-light">
                <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                    Copyright &copy; 2018. Created by
                    <a href="http://www.tooplate.com" class="text-white tm-footer-link">Tooplate</a> | Distributed by <a href="https://themewagon.com" class="text-white tm-footer-link">ThemeWagon</a>
                </p>
            </div>
        </footer>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="jquery-ui-datepicker/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
