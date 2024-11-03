<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL|E_STRICT);

$pathInPieces = 'invent2024'; 
require_once ($_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/' . 'akm_config/akm_config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/' . 'akm_config/akm_koneksi.php');
// require_once($_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/inv_config/akm_fungsi.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/' . $pathInPieces . '/' . 'akm_config/geoplugin.class.php');

if (isset($_GET['cek_login'])) {
    include ($_SERVER['DOCUMENT_ROOT']. "/" . $pathInPieces ."/akm_frontend/akm_stream/akm_ceklogin.php");
} elseif (isset($_GET['logout'])) {
    include ($_SERVER['DOCUMENT_ROOT']. "/" . $pathInPieces ."/inv_frontend/inv_login/logout.php");
} elseif (isset($_GET['hal'])) { // Periksa jika 'hal' diset
    if ($_GET['hal'] == 'produk') {
        include ("inv2024/frontend/product.php");
    } elseif ($_GET['hal'] == 'pinjam') {
        include ("inv2024/frontend/pinjam.php");
    } elseif ($_GET['hal'] == 'kembali') {
        include ("inv2024/frontend/kembali.php");
    } elseif ($_GET['hal'] == 'tambah') {
        include ("inv2024/frontend/add-product.php");
    } elseif ($_GET['hal'] == 'profil') {
        include ("akmaliah2016/frontend/profil.php");
    } elseif ($_GET['hal'] == 'seni_sastra') {
        include ("akmaliah2016/frontend/modul/seni_sastra/seni_sastra.php");
    } elseif ($_GET['hal'] == 'taklim') {
        include ("akmaliah2016/frontend/modul/taklim/index.php");
    } elseif ($_GET['hal'] == 'kajian_online') {
        include ("akmaliah2016/backend/modul/taklim_selasa/salikin_online.php");
    } elseif ($_GET['hal'] == 'admin_pass') {
        include ("akmaliah2016/backend/modul/user/duser.php");
    } elseif ($_GET['hal'] == 'admin-taklim') {
        include ("akmaliah2016/frontend/modul/taklim/login.php");
    } elseif ($_GET['hal'] == 'cek_admin') {
        include ("akmaliah2016/frontend/modul/taklim/cek_login.php");
    } elseif ($_GET['hal'] == 'cek_sema') {
        include ("akmaliah2016/frontend/modul/live/login-stream/cek_login.php");
    } elseif ($_GET['hal'] == 'logout') {
        include ("inv_frontend/inv_login/logout.php");
    } elseif ($_GET['hal'] == 'pendar-hikmah') {
        include ("akmaliah2016/frontend/modul/pendar/pendar.php");
    } elseif ($_GET['hal'] == 'form_salikin') {
        include ("akmaliah2016/backend/modul/salikin/salikin.php");
    } elseif ($_GET['hal'] == 'proses_salikin') {
        include ("akmaliah2016/backend/modul/salikin/proses_salikin.php");
    } elseif ($_GET['hal'] == 'barcode') {
        echo '<script>window.location.href = "http://' . $_SERVER['HTTP_HOST'] . '/absen";</script>';
    } elseif ($_GET['hal'] == 'user') {
        include ("inv2024/frontend/user_dashboard.php");
    } elseif ($_GET['hal'] == 'admin') {
        include ("inv2024/frontend/admin_dashboard.php");
    } elseif ($_GET['hal'] == 'daftar_admin') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/inv_frontend/inv_login/daftar_admin.php");
    } elseif ($_GET['hal'] == 'daftar_user') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/inv_frontend/inv_login/daftar_user.php");
    } elseif ($_GET['hal'] == 'login') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/inv_frontend/inv_login/index.php");
    } elseif ($_GET['hal'] == 'forgot') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/akm_frontend/ikra_login/forgot_password.php");
    } elseif ($_GET['hal'] == 'reset') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/akm_frontend/ikra_login/reset_password.php");
    } elseif ($_GET['hal'] == 'ceklogin') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/inv_frontend/inv_login/ceklogin.php");
    } elseif ($_GET['hal'] == 'e') {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces . "/#");
    } elseif ($_GET['hal'] == 'streaming_kajian') {
        include ("akmaliah/akm_index.php");
    } else {
        include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/inv_frontend/beranda.php");
    }
} else {
    include ($_SERVER['DOCUMENT_ROOT']."/" . $pathInPieces ."/inv_frontend/beranda.php");
}
?>