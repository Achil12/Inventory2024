<?php
$host = 'localhost';
$username = 'qnap';
$password = 'X9wMFjVDykP4';
$database = 'inv';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
