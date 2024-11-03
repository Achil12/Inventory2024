<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ?hal=login");
    exit();
}

// Ambil nama pengguna dari sesi
$user_name = $_SESSION['user_id'];

// Koneksi ke database dan ambil data barang
$items = [];
$query = "SELECT id_barang, kode_barang, nama_barang, kode_barcode, jumlah_barang FROM barang"; 
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah_pinjam = $_POST['jumlah_pinjam']; 
    $foto_barang = $_FILES['foto_barang']['name'];
    $peminjam = $_POST['peminjam'];
    $tanggal_pinjam = date('Y-m-d'); 
    $status_pinjam = 'Dipinjam'; 
    $rule_pinjam = $_POST['rule_pinjam']; 
    $id_barang = $_POST['id_barang'];

    // Check current quantity in the barang table
    $checkQuery = "SELECT jumlah_barang FROM barang WHERE kode_barang = ?";
    $stmt = $conn->prepare($checkQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_barang);
    $stmt->execute();
    $stmt->bind_result($currentJumlahBarang);
    $stmt->fetch();
    $stmt->close();

    // Validate if there is enough stock
    if ($currentJumlahBarang >= $jumlah_pinjam) {
        // Check if the item is already borrowed by the same person
        $checkPinjamQuery = "SELECT jumlah_pinjam FROM pinjam WHERE kode_barang = ? AND peminjam = ?";
        $checkPinjamStmt = $conn->prepare($checkPinjamQuery);
        if (!$checkPinjamStmt) {
            die("Prepare failed: " . $conn->error);
        }

        $checkPinjamStmt->bind_param("ss", $kode_barang, $peminjam);
        $checkPinjamStmt->execute();
        $checkPinjamStmt->bind_result($existingJumlahPinjam);
        $checkPinjamStmt->fetch();
        $checkPinjamStmt->close();

        if ($existingJumlahPinjam !== null) {
            // Update existing entry
            $newJumlahPinjam = $existingJumlahPinjam + $jumlah_pinjam;
            $updatePinjamQuery = "UPDATE pinjam SET jumlah_pinjam = ? WHERE kode_barang = ? AND peminjam = ?";
            $updatePinjamStmt = $conn->prepare($updatePinjamQuery);
            if (!$updatePinjamStmt) {
                die("Prepare failed: " . $conn->error);
            }

            $updatePinjamStmt->bind_param("iss", $newJumlahPinjam, $kode_barang, $peminjam);
            $updatePinjamStmt->execute();
            $updatePinjamStmt->close();
        } else {
            // New entry for borrowing
            $target_dir = "/var/www/html/inv/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . basename($_FILES["foto_barang"]["name"]);

            if (move_uploaded_file($_FILES["foto_barang"]["tmp_name"], $target_file)) {
                // Prepare insert query
                $stmt = $conn->prepare("INSERT INTO pinjam (id_barang, kode_barang, kode_barcode, nama_barang, foto_barang, peminjam, tanggal_pinjam, status_pinjam, rule_pinjam, jumlah_pinjam) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }

                // Bind parameters
                $stmt->bind_param("issssssssi", $id_barang, $kode_barang, $kode_barang, $nama_barang, $target_file, $peminjam, $tanggal_pinjam, $status_pinjam, $rule_pinjam, $jumlah_pinjam);
                
                if ($stmt->execute()) {
                    // Update jumlah_barang in barang table
                    $newJumlahBarang = $currentJumlahBarang - $jumlah_pinjam;
                    $updateQuery = "UPDATE barang SET jumlah_barang = ? WHERE kode_barang = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    if (!$updateStmt) {
                        die("Prepare failed: " . $conn->error);
                    }

                    $updateStmt->bind_param("is", $newJumlahBarang, $kode_barang);
                    $updateStmt->execute();
                    $updateStmt->close();

                    echo "<div class='alert alert-success'>Peminjaman berhasil!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }

                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error uploading file.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Jumlah pinjam melebihi stok yang tersedia.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Peminjaman Barang - Dashboard Admin Template</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="jquery-ui-datepicker/jquery-ui.min.css" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
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
        <div class="row tm-mt-big">
            <div class="col-xl-8 col-lg-10 col-md-12 col-sm-12">
                <div class="bg-white tm-block">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Peminjaman Barang</h2>
                        </div>
                    </div>
                    <div class="row mt-4 tm-edit-product-row">
                        <div class="col-xl-7 col-lg-7 col-md-12">
                            <form action="" method="POST" class="tm-edit-product-form" enctype="multipart/form-data">
                                <input type="hidden" id="id_barang" name="id_barang" required>
                                <div class="input-group mb-3">
                                    <label for="kode_barcode" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Kode Barcode</label>
                                    <input id="kode_barcode" name="kode_barcode" type="text" class="form-control validate col-xl-9 col-lg-8 col-md-8 col-sm-7" required readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="kode_barang" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 mb-2">Kode Barang</label>
                                    <input id="kode_barang" name="kode_barang" type="text" class="form-control validate col-xl-9 col-lg-8 col-md-8 col-sm-7" required readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="nama_barang" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Nama Barang</label>
                                    <select id="nama_barang" name="nama_barang" class="form-control col-xl-9 col-lg-8 col-md-8 col-sm-7" required onchange="updateKodeFields()">
                                        <option value="" disabled selected>Pilih Barang</option>
                                        <?php foreach ($items as $item): ?>
                                            <option value="<?php echo $item['kode_barang']; ?>" data-barcode="<?php echo $item['kode_barcode']; ?>" data-jumlah="<?php echo $item['jumlah_barang']; ?>" data-id="<?php echo $item['id_barang']; ?>">
                                                <?php echo $item['nama_barang']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="jumlah_pinjam" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Jumlah Pinjam</label>
                                    <input id="jumlah_pinjam" name="jumlah_pinjam" type="number" class="form-control col-xl-9 col-lg-8 col-md-8 col-sm-7" required min="1">
                                </div>
                                <div class="input-group mb-3">
                                    <label for="peminjam" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Nama Peminjam</label>
                                    <input id="peminjam" name="peminjam" type="text" class="form-control col-xl-9 col-lg-8 col-md-8 col-sm-7" required value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="rule_pinjam" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Rule Pinjam</label>
                                    <input id="rule_pinjam" name="rule_pinjam" type="text" class="form-control col-xl-9 col-lg-8 col-md-8 col-sm-7" required readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="foto_barang" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Foto Barang</label>
                                    <input id="foto_barang" name="foto_barang" type="file" class="form-control col-xl-9 col-lg-8 col-md-8 col-sm-7" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Pinjam Barang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        function generateRandomID() {
            return Math.floor(Math.random() * 1000000); // Random number up to 1 million
        }

        // Update Kode Barcode dan jumlah barang yang bisa dipinjam
        function updateKodeFields() {
            var select = document.getElementById('nama_barang');
            var selectedOption = select.options[select.selectedIndex];

            // Set kode_barang
            var kodeBarang = selectedOption.value;
            document.getElementById('kode_barang').value = kodeBarang;

            // Set kode_barcode
            var kodeBarcode = selectedOption.getAttribute('data-barcode');
            document.getElementById('kode_barcode').value = kodeBarcode;

            // Set id_barang
            var idBarang = selectedOption.getAttribute('data-id');
            document.getElementById('id_barang').value = idBarang;

            // Update jumlah_barang
            var jumlahBarang = selectedOption.getAttribute('data-jumlah');
            var jumlahPinjamInput = document.getElementById('jumlah_pinjam');
            jumlahPinjamInput.setAttribute('max', jumlahBarang);
            jumlahPinjamInput.value = "";

            // Set rule_pinjam
            document.getElementById('rule_pinjam').value = generateRandomID();
        }
    </script>
</body>
</html>
