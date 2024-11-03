<?php 
// cek_login.php
session_start();
include 'db.php'; // Mengimpor koneksi database

$error_message = '';

// Fungsi untuk mendapatkan alamat IP pengguna
function getUserIP() {
    return !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : $_SERVER['REMOTE_ADDR'];
}

// Fungsi untuk mendapatkan merek perangkat dari database
function getDeviceBrand($user_agent) {
    global $conn;
    $brand = "Unknown Brand";
    $query = "SELECT brand_name FROM brands";
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (stripos($user_agent, $row['brand_name']) !== false) {
                $brand = $row['brand_name'];
                break;
            }
        }
    }
    return $brand;
}

// Fungsi untuk mendapatkan informasi perangkat
function getDeviceInfo() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $device = "Unknown Device";
    $brand = getDeviceBrand($user_agent);

    if (preg_match('/android/i', $user_agent)) {
        $device = "Android Device";
    } elseif (preg_match('/iPhone/i', $user_agent)) {
        $device = "iPhone";
    } elseif (preg_match('/iPad/i', $user_agent)) {
        $device = "iPad";
    } elseif (preg_match('/Windows/i', $user_agent)) {
        $device = "Windows Device";
    } elseif (preg_match('/Macintosh/i', $user_agent)) {
        $device = "Mac";
    }

    return [
        'device' => $device,
        'brand' => $brand,
        'user_agent' => $user_agent,
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $password = $_POST['pwd_admin'] ?? '';
    $ip_address = getUserIP();
    $device_info = getDeviceInfo();
    $device = $device_info['device'];
    $brand = $device_info['brand'];
    $user_agent = $device_info['user_agent'];

    // Mengambil data pengguna dari database
    $query = "SELECT * FROM users WHERE nama = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['passwd'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['jabatan'] = $user['jabatan'];
            $_SESSION['id_jabatan'] = $user['id_jabatan'];

            // Redirect berdasarkan peran pengguna
            if ($user['id_jabatan'] == 1 || $user['id_jabatan'] == 2) { // Admin
                header("Location: ?hal=admin");
            } elseif ($user['id_jabatan'] == 3) { // User
                header("Location: ?hal=user");
            } else {
                header("Location: ?hal=login");
            }
            exit();
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Username tidak ditemukan!";
    }

    // Simpan pesan kesalahan ke dalam session
    $_SESSION['error_message'] = $error_message;
    // Redirect kembali ke halaman login
    header("Location: ?hal=login");
    exit();
}
?>
