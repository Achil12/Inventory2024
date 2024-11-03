<?php
/* config Database */

error_reporting(0);
session_set_cookie_params(0);
date_default_timezone_set("Asia/Jakarta"); //Setting Waktu Wilayah

/* Setting Mysql Server */
define("HOST", "localhost"); // Host Server
define("USER", "qnap"); // Username localhost
define("PASSWORD", "X9wMFjVDykP4"); // Password localhost
define("DATABASE", "inv"); // Nama database lokalhost
define("IP_SERVER", "192.168.13.250"); // Nama database

/* Setting Directori Server */
define("NAME_DIR_SERVER", "inv20204/"); // Nama directori root server
define("NAME_DIR_UPLOAD", "inv_upload/"); // Nama directori UPLOAD server
define("DIR_SERVER", $_SERVER['DOCUMENT_ROOT'].'/'.NAME_DIR_SERVER.'/'); // Nama directori root server
define("DIR_UPLOAD", DIR_SERVER.NAME_DIR_UPLOAD.'/'); // ROOT folder UPLOAD
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");