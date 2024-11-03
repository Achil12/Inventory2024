<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ?hal=login");
    exit();
}

// Definisikan fungsi generateKode
function generateKode($conn) {
    return 'PAS-' . time() . '-' . rand(1000, 9999);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ambil data kode barang untuk dropdown
$kode_barang_options = '';
$query_kode_barang = "SELECT kode_barang, nama_barang FROM pinjam WHERE status_pinjam = 'Dipinjam'";
$result_kode_barang = $conn->query($query_kode_barang);

if ($result_kode_barang->num_rows > 0) {
    while ($row = $result_kode_barang->fetch_assoc()) {
        $kode_barang_options .= "<option value='" . $row['kode_barang'] . "'>" . $row['kode_barang'] . " - " . $row['nama_barang'] . "</option>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kode_barang'])) {
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah_pinjam = $_POST['jumlah_pinjam'];
    $tanggal_pengembalian = date('Y-m-d');

    $query_check = "SELECT * FROM pinjam WHERE kode_barang = '$kode_barang'";
    $result = $conn->query($query_check);

    if ($result->num_rows > 0) {
        $data_pinjam = $result->fetch_assoc();
        $foto_barang = $data_pinjam['foto_barang'];

        // Siapkan untuk upload foto
        $uploaded_photos = [];
        if (empty($_FILES['foto_barang']['name'][0])) {
            echo "<script>alert('Anda harus mengupload minimal 1 foto.');</script>";
        } else {
            for ($i = 0; $i < count($_FILES['foto_barang']['name']); $i++) {
                if (isset($_FILES['foto_barang']['name'][$i]) && $_FILES['foto_barang']['name'][$i] != "") {
                    $target_dir = "/var/www/html/inv/uploads/";
                    $target_file = $target_dir . basename($_FILES['foto_barang']['name'][$i]);

                    // Pindahkan file yang diupload ke direktori yang ditentukan
                    if (move_uploaded_file($_FILES['foto_barang']['tmp_name'][$i], $target_file)) {
                        $uploaded_photos[] = $target_file;
                    } else {
                        echo "<script>alert('Error uploading photo: " . $_FILES['foto_barang']['name'][$i] . "');</script>";
                    }
                }
            }
        }

        // Update status barang di tabel barang dan tambahkan jumlahnya
        $query_update_barang = "UPDATE barang 
                                SET status_pinjam = 'Tersedia', 
                                    jumlah_barang = jumlah_barang + $jumlah_pinjam 
                                WHERE kode_barang = '$kode_barang'";

        if ($conn->query($query_update_barang) === TRUE) {
            // Hapus data pinjam di tabel pinjam
            $query_hapus_pinjam = "DELETE FROM pinjam 
                                    WHERE kode_barang = '$kode_barang'";

            if ($conn->query($query_hapus_pinjam) === TRUE) {
                echo "<script>alert('Pengembalian berhasil! Barang telah dipindahkan kembali ke tabel barang.');</script>";

                // Tampilkan foto yang diupload
                foreach ($uploaded_photos as $photo) {
                    echo "<img src='$photo' alt='Uploaded Photo' style='width: 100px; height: auto; margin: 5px;'>";
                }
            } else {
                echo "<script>alert('Error saat menghapus data pinjam: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error saat mengupdate data barang: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Barang tidak ditemukan dalam data peminjaman.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Barang</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .text-center {
            text-align: center;
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
                            <h2 class="tm-block-title">Pengembalian Barang</h2>
                        </div>
                    </div>
                    <div class="row mt-4 tm-edit-product-row">
                        <div class="col-xl-12">
                            <div class="form-container">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="form-group mb-3">
                                        <label for="kode_barang" class="col-form-label">Kode Barang:</label>
                                        <select id="kode_barang" name="kode_barang" class="form-control" required>
                                            <option value="">Pilih Kode Barang</option>
                                            <?php echo $kode_barang_options; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_barang" class="col-form-label">Nama Barang:</label>
                                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="jumlah_pinjam" class="col-form-label">Jumlah yang Dipinjam:</label>
                                        <input type="number" id="jumlah_pinjam" name="jumlah_pinjam" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="foto_barang" class="col-form-label">Upload Foto Barang:</label>
                                        <input type="file" name="foto_barang[]" class="form-control" multiple required>
                                        <small class="form-text text-muted">Maksimal 3 foto, minimal 1 foto.</small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">Kembalikan Barang</button>
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
                    Copyright &copy; 2023. Created by
                    <a href="http://www.tooplate.com" class="text-white tm-footer-link">Tooplate</a>
                </p>
            </div>
        </footer>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
