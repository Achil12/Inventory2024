-- --------------------------------------------------------
-- Host:                         192.168.13.250
-- Versi server:                 10.11.6-MariaDB-0+deb12u1 - Debian 12
-- OS Server:                    debian-linux-gnu
-- HeidiSQL Versi:               12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk inv
CREATE DATABASE IF NOT EXISTS `inv` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `inv`;

-- membuang struktur untuk table inv.barang
CREATE TABLE IF NOT EXISTS `barang` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `kode_barcode` varchar(100) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `status_pinjam` enum('tersedia','pinjam','kembali','kosong') NOT NULL DEFAULT 'tersedia',
  `jumlah_barang` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_barang`) USING BTREE,
  UNIQUE KEY `kode` (`kode_barang`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel inv.barang: ~1 rows (lebih kurang)
INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `foto`, `kode_barcode`, `tanggal_masuk`, `status_pinjam`, `jumlah_barang`) VALUES
	(9, 'PAS0001', 'aqua', '/var/www/html/inv_upload/uploads/img/672012d6cb8b3.png', 'PAS7D536DBA', '2024-11-01', 'tersedia', 1);

-- membuang struktur untuk table inv.brands
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel inv.brands: ~25 rows (lebih kurang)
INSERT INTO `brands` (`id`, `brand_name`, `description`) VALUES
	(1, 'Samsung', 'Merek smartphone asal Korea Selatan.'),
	(2, 'Apple', 'Merek terkenal dengan produk iPhone dan Mac.'),
	(3, 'Xiaomi', 'Merek smartphone asal Tiongkok.'),
	(4, 'Google', 'Merek perangkat Nexus dan Pixel.'),
	(5, 'OnePlus', 'Merek smartphone premium asal Tiongkok.'),
	(6, 'Huawei', 'Merek smartphone dengan inovasi kamera.'),
	(7, 'Oppo', 'Merek terkenal dengan fitur kamera.'),
	(8, 'Sony', 'Merek elektronik dengan produk smartphone.'),
	(9, 'Nokia', 'Merek telepon legendaris yang kembali ke pasar smartphone.'),
	(10, 'Microsoft', 'Merek yang dikenal dengan perangkat Surface.'),
	(11, 'LG', 'Merek elektronik yang juga memproduksi smartphone.'),
	(12, 'Realme', 'Merek smartphone yang menawarkan value.'),
	(13, 'Vivo', 'Merek smartphone dengan fokus pada kamera.'),
	(14, 'Lenovo', 'Merek elektronik dengan produk laptop dan smartphone.'),
	(15, 'ASUS', 'Merek yang terkenal dengan produk laptop dan smartphone.'),
	(16, 'Dell', 'Laptop/PC'),
	(17, 'HP', 'Laptop/PC'),
	(18, 'Lenovo', 'Laptop/PC'),
	(19, 'Asus', 'Laptop/PC'),
	(20, 'Acer', 'Laptop/PC'),
	(21, 'Apple', 'Laptop'),
	(22, 'Microsoft', 'Laptop/PC'),
	(23, 'Razer', 'Laptop'),
	(24, 'MSI', 'Laptop/PC'),
	(25, 'Toshiba', 'Laptop/PC');

-- membuang struktur untuk table inv.jabatan
CREATE TABLE IF NOT EXISTS `jabatan` (
  `id_jabatan` int(11) NOT NULL AUTO_INCREMENT,
  `status_jabatan` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id_jabatan`) USING BTREE,
  KEY `aturan` (`status_jabatan`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Membuang data untuk tabel inv.jabatan: ~3 rows (lebih kurang)
INSERT INTO `jabatan` (`id_jabatan`, `status_jabatan`, `keterangan`) VALUES
	(1, 'Superadmin', NULL),
	(2, 'admin', NULL),
	(3, 'users', NULL);

-- membuang struktur untuk table inv.log_login
CREATE TABLE IF NOT EXISTS `log_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('success','failure') DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `log_login_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel inv.log_login: ~0 rows (lebih kurang)

-- membuang struktur untuk table inv.pinjam
CREATE TABLE IF NOT EXISTS `pinjam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `peminjam` varchar(100) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `status_pinjam` enum('Dipinjam','Kembali') NOT NULL DEFAULT 'Dipinjam',
  `rule_pinjam` varchar(100) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL DEFAULT 0,
  `tanggal_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kode_barang` varchar(255) DEFAULT NULL,
  `kode_barcode` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `foto_barang` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_kode_barang` (`id_barang`) USING BTREE,
  CONSTRAINT `FK_pinjam_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel inv.pinjam: ~0 rows (lebih kurang)

-- membuang struktur untuk table inv.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `passwd` varchar(255) DEFAULT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE,
  KEY `id_jabatan` (`id_jabatan`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel inv.users: ~1 rows (lebih kurang)
INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `jabatan`, `id_jabatan`, `passwd`, `qrcode`) VALUES
	(1, 'admin', 'sad@ad.com', NULL, 'Superadmin', 2, '$2y$10$uoo9VX8u.dLCIjRJMSEBquA/RCNdl5WUpEZTVxE1IMARD71mXW8Ua', 'qrcodes/6723ec0cf2c39.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
