<?php
// Mengaktifkan tampilan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/** Konfigurasi Koneksi ke Data Server **/

$pathInPieces = 'invent2024';
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/' . 'akm_config/akm_config.php';

date_default_timezone_set("Asia/Jakarta");
$koneksi = new mysqli(HOST, USER, PASSWORD, DATABASE); // Melakukan koneksi ke database berdasarkan konfigurasi diatas
if($koneksi->connect_error){
trigger_error('Koneksi ke database gagal: ' . $koneksi->connect_error, E_USER_ERROR); // Jika koneksi gagal, tampilkan pesan "Koneksi ke database gagal"
}

	
?>