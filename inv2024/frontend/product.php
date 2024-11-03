<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ?hal=login");
    exit();
}

// Retrieve user ID from session
$userId = $_SESSION['user_id'];

// Mengambil data pengguna dari tabel users
$queryUser = "SELECT nama, jabatan, id_jabatan FROM users WHERE id = ?";
$stmtUser = $conn->prepare($queryUser);
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

$userData = null;
if ($resultUser && $resultUser->num_rows > 0) {
    $userData = $resultUser->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Tentukan apakah pengguna adalah admin (misalnya id_jabatan = 1)
$isAdmin = $userData['id_jabatan'] === '1'; // Sesuaikan ID jabatan untuk admin
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manajemen Peminjaman Barang PAS</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
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

            <div class="row tm-content-row tm-mt-big">
                <div class="col-xl-8 col-lg-12 tm-md-12 tm-sm-12 tm-col">
                    <div class="bg-white tm-block h-100">
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <h2 class="tm-block-title d-inline-block">Manajemen Peminjaman Barang</h2>
                                <p>Welcome, <?= htmlspecialchars($userData['nama']) ?> (<?= htmlspecialchars($userData['jabatan']) ?>)</p>
                            </div>
                            <?php if ($isAdmin): // Admin dapat menambahkan produk ?>
                                <div class="col-md-4 col-sm-12 text-right">
                                    <a href="add-product.php" class="btn btn-small btn-primary">Add New Product</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped tm-table-striped-even mt-3">
                                <thead>
                                    <tr class="tm-bg-gray">
                                        <th scope="col">Kode Barang</th>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Foto</th>
                                        <th scope="col">Kode Barcode</th>
                                        <th scope="col">Status Barang</th>
                                        <th scope="col">Jumlah Barang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Mengambil data barang dari database
                                    $query = "SELECT * FROM barang ORDER BY id_barang";
                                    $result = $conn->query($query);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()):
                                            // Tentukan status pinjam berdasarkan jumlah barang
                                            $statusPinjam = $row['jumlah_barang'] > 0 ? $row['status_pinjam'] : 'Kosong';
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                <td>
                                                    <img src="inv_upload/uploads/img/<?= htmlspecialchars(basename($row['foto'])) ?>" alt="Foto" width="100">
                                                </td>
                                                <td><?= htmlspecialchars($row['kode_barcode']) ?></td>
                                                <td><?= htmlspecialchars($statusPinjam) ?></td>
                                                <td><?= htmlspecialchars($row['jumlah_barang']) ?></td>
                                            </tr>
                                        <?php endwhile;
                                    } else {
                                        echo "<tr><td colspan='6'>Tidak ada data barang.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-12 tm-md-12 tm-sm-12 tm-col">
                    <div class="bg-white tm-block h-100">
                        <h2 class="tm-block-title d-inline-block">Peringatan Peminjaman</h2>
                        <table class="table table-hover table-striped mt-3">
                            <tbody>
                                <?php
                                // Mengambil data peminjaman untuk user yang sedang login
                                $queryPinjam = "
                                SELECT p.*, b.nama_barang
                                FROM pinjam p
                                JOIN barang b ON p.kode_barang = b.kode_barang
                                WHERE p.peminjam = ?";
                                
                                $stmtPinjam = $conn->prepare($queryPinjam);
                                $stmtPinjam->bind_param("s", $userData['nama']);
                                $stmtPinjam->execute();
                                $resultPinjam = $stmtPinjam->get_result();

                                if ($resultPinjam && $resultPinjam->num_rows > 0) {
                                    while ($row = $resultPinjam->fetch_assoc()) {
                                        $tanggalPinjam = new DateTime($row['tanggal_pinjam']);
                                        $tanggalSekarang = new DateTime();
                                        $selisihHari = $tanggalSekarang->diff($tanggalPinjam)->days;

                                        if ($selisihHari > 7) {
                                            echo "<tr>";
                                            echo "<td>{$row['nama_barang']} - Peminjaman lebih dari 7 hari</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr><td>Tidak ada data peminjaman.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="row tm-mt-small">
                <div class="col-12 font-weight-light">
                    <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                        Copyright &copy; 2018. Created by
                        <a href="http://www.tooplate.com" class="text-white tm-footer-link">Tooplate</a>
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
