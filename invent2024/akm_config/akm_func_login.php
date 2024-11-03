<?php
require_once ('akm_koneksi.php');



   function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
function data_akses($koneksi, $id_jabatan)
{
    $query  = "SELECT * FROM akm_jabatan WHERE id_jabatan IN (100,101,102,103)";
    $result = $koneksi->query($query);
    $data   = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}


/*********** 10 OKTOBER 2016 **********************/
function get_client_ip_env() {
$ipaddress = '';
if (getenv('HTTP_CLIENT_IP'))
$ipaddress = getenv('HTTP_CLIENT_IP');
else if(getenv('HTTP_X_FORWARDED_FOR'))
$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
else if(getenv('HTTP_X_FORWARDED'))
$ipaddress = getenv('HTTP_X_FORWARDED');
else if(getenv('HTTP_FORWARDED_FOR'))
$ipaddress = getenv('HTTP_FORWARDED_FOR');
else if(getenv('HTTP_FORWARDED'))
$ipaddress = getenv('HTTP_FORWARDED');
else if(getenv('REMOTE_ADDR'))
$ipaddress = getenv('REMOTE_ADDR');
else
$ipaddress = 'UNKNOWN';

return $ipaddress;
}
if (!function_exists('get_client_ip_server')) {
    function get_client_ip_server() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}


/** Interval Waktu Login Taklim Selasa (2jam) **/
function get_interval_login($now){
   
    $interval_login = $now - (2*60*60);

}




function get_attr_online(){
$now = time();
$ip_salikin = get_client_ip_server();
$browser_salikin = $_SERVER['HTTP_USER_AGENT'];
$user_remotes = ($ip_salikin);
}



function cek_jamaahmasa($koneksi, $nis){

$sql = "SELECT a.NIS, a.nama_lengkap, a.id_taklim as taklim, a.foto_thumb FROM akm_jamaah a WHERE a.NIS = ? LIMIT 1";

    
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('s', $nis); // Bind "$nis" to parameter.
    $stmt->execute(); // Menjalankan perintah yang sudah disiapkan.
    $stmt->store_result();

     // mendapatkan variabel-variabel dari hasilnya.

    $stmt->bind_result($db_nis, $db_nama, $db_taklim,$db_foto_t);
    $stmt->fetch();

    if ($stmt->num_rows == 0){
        return false;
    } else {             
               return true;
        }
    }


function cek_izin_online2($koneksi,$nis,$passw){



$sql = "SELECT a.permanen,a.request,a.nama,a.NIS, a.passwords, a.rule, a.user_status, a.nama_lengkap, a.id_taklim as taklim, a.foto_thumb FROM akm_jamaah a WHERE a.NIS = ? LIMIT 1";


//$sql = "SELECT a.permanen,a.request,a.nama,a.NIS, a.passwords, a.rule, a.user_status, b.nama_lengkap, b.id_taklim as taklim, b.foto_thumb FROM akm_login a LEFT JOIN akm_jamaah b ON (a.NIS = b.NIS) WHERE a.NIS = ? LIMIT 1";
    
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('s', $nis); // Bind "$nis" to parameter.
    $stmt->execute(); // Menjalankan perintah yang sudah disiapkan.
    $stmt->store_result();

     // mendapatkan variabel-variabel dari hasilnya.

    $stmt->bind_result($db_permanen,$db_request,$db_salikin,$db_nis, $db_password, $db_rule, $db_status, $db_nama, $db_taklim,$db_foto_t);
    $stmt->fetch();

    if ($stmt->num_rows == 0){
        return 'NIS Anda Belum Terdaftar Dalam Pengkajian Selasa Malam !';
    } else {
        if ($db_password==$passw) {
            
            if ($db_rule=3 AND $db_request=='Y') {

                if ($db_permanen=='Y') {
                 
                $_SESSION['permanen'] = $db_permanen;   
                $_SESSION['nis_s'] = $db_nis;
                $_SESSION['rule'] = $db_rule;
                $_SESSION['user_status'] = $db_status;
                $_SESSION['nama_s'] = $db_nama.$db_salikin;
                $_SESSION['taklim'] = $db_taklim;
                $_SESSION['foto_t'] = $db_foto_t;
                $_SESSION['salikin'] = $_SESSION['nama_s'];
                $_SESSION['login_time'] = time();
                $_SESSION['page'] = $pages;
                return 'unlimited';

                } else {
                
                $_SESSION['permanen'] = $db_permanen; 
                $_SESSION['nis_s'] = $db_nis;
                $_SESSION['rule'] = $db_rule;
                $_SESSION['user_status'] = $db_status;
                $_SESSION['nama_s'] = $db_nama.$db_salikin;
                $_SESSION['taklim'] = $db_taklim;
                $_SESSION['foto_t'] = $db_foto_t;
                $_SESSION['salikin'] = $_SESSION['nama_s'];
                $_SESSION['login_time'] = time();
                $_SESSION['page'] = $pages;
                return 'limited';

                }

            } else {
                return 'Anda Belum Mendapatkan Izin !';
            }

        } else {
            return 'Password Salah !';
        }
    }


}


function cek_izin_online($koneksi,$nis,$passw){



$sql = "SELECT a.permanen,a.request,a.nama,a.NIS, a.passwords, a.rule, a.user_status, a.nama_lengkap, a.id_taklim as taklim, a.foto_thumb FROM akm_jamaah a WHERE a.NIS = ? LIMIT 1";


//$sql = "SELECT a.permanen,a.request,a.nama,a.NIS, a.passwords, a.rule, a.user_status, b.nama_lengkap, b.id_taklim as taklim, b.foto_thumb FROM akm_login a LEFT JOIN akm_jamaah b ON (a.NIS = b.NIS) WHERE a.NIS = ? LIMIT 1";
    
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('s', $nis); // Bind "$nis" to parameter.
    $stmt->execute(); // Menjalankan perintah yang sudah disiapkan.
    $stmt->store_result();

     // mendapatkan variabel-variabel dari hasilnya.

    $stmt->bind_result($db_permanen,$db_request,$db_salikin,$db_nis, $db_password, $db_rule, $db_status, $db_nama, $db_taklim,$db_foto_t);
    $stmt->fetch();

    if ($stmt->num_rows == 0){
        return 'NIS Anda Belum Terdaftar Dalam Pengkajian Selasa Malam !';
    } else {
        if ($db_password==$passw) {
            
            if ($db_rule=3 AND $db_request=='Y') {

                if ($db_permanen=='Y') {
                 
                $_SESSION['permanen'] = $db_permanen;   
                $_SESSION['nis_s'] = $db_nis;
                $_SESSION['rule'] = $db_rule;
                $_SESSION['user_status'] = $db_status;
                $_SESSION['nama_s'] = $db_nama.$db_salikin;
                $_SESSION['taklim'] = $db_taklim;
                $_SESSION['foto_t'] = $db_foto_t;
                $_SESSION['salikin'] = $_SESSION['nama_s'];
                $_SESSION['login_time'] = time();
                $_SESSION['page'] = $pages;
                return 'unlimited';

                } else {
                
                $_SESSION['permanen'] = $db_permanen; 
                $_SESSION['nis_s'] = $db_nis;
                $_SESSION['rule'] = $db_rule;
                $_SESSION['user_status'] = $db_status;
                $_SESSION['nama_s'] = $db_nama.$db_salikin;
                $_SESSION['taklim'] = $db_taklim;
                $_SESSION['foto_t'] = $db_foto_t;
                $_SESSION['salikin'] = $_SESSION['nama_s'];
                $_SESSION['login_time'] = time();
                $_SESSION['page'] = $pages;
                return 'limited';

                }

            } else {
                return 'Anda Belum Mendapatkan Izin !';
            }

        } else {
        
        $_SESSION['nama_s'] = $db_nama.$db_salikin;
                return 'Password Salah !';

        }
    }


}


















function login_selasa($koneksi,$input){
         //$pass_admin = base64_encode($var_pass);
$pass_salikin = $input;
    $sql = "SELECT a.nama,a.NIS, a.passwords, a.rule, a.user_status, b.nama_lengkap, b.id_taklim as taklim, b.foto_thumb FROM akm_login a LEFT JOIN akm_jamaah b ON (a.NIS = b.NIS) WHERE a.passwords = ? LIMIT 1";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('s', $pass_salikin); // Bind "$nis" to parameter.
    $stmt->execute(); // Menjalankan perintah yang sudah disiapkan.
    $stmt->store_result();

    // mendapatkan variabel-variabel dari hasilnya.

    $stmt->bind_result($db_salikin,$db_nis, $db_password, $db_rule, $db_status, $db_nama, $db_taklim,$db_foto_t);
    $stmt->fetch();
     if ($stmt->num_rows == 1)
        {
        if ($db_password == $pass_salikin AND ($db_rule==3 OR $db_rule == 1))
            {
            $user_browser = $_SERVER['HTTP_USER_AGENT'];
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $_SESSION['nis_s'] = $db_nis;
            $_SESSION['rule'] = $db_rule;
            $_SESSION['user_status'] = $db_status;
            $_SESSION['nama_s'] = $db_nama.$db_salikin;
            $_SESSION['taklim'] = $db_taklim;
            $_SESSION['foto_t'] = $db_foto_t;
            $_SESSION['salikin'] = $_SESSION['nama_s'];
            $_SESSION['login_time'] = time();
            $_SESSION['page'] = $pages;    
            return true;
            }
          else
            {
            return false;
            }
        }
      else
        {
        return false;
        }
}



if (!function_exists('success_online')) {
    function success_online($koneksi, $nis, $nama, $ket, $lat, $lon) {
        $now = time();
        $hari_ini = date('Y-m-d H:i:s');
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $current_page = $_SERVER['REQUEST_URI'];
        $user_status = '1';
        $user_remotes = ($user_ip);
        
        $sql = "INSERT INTO akm_online (NIS, waktu, nama, aktif, client, timer, ip, browser, ket, x, y) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        
        if (!$stmt) {
            return false;
        } else {
            $stmt->bind_param("ssssssssss", $nis_s, $nama_s, $user_stat, $user_rem, $time, $ip_s, $brows, $kets, $lats, $lons);
            $nis_s = $nis;
            $nama_s = $nama;
            $user_stat = $user_status;
            $user_rem = $user_remotes;
            $time = $now;
            $ip_s = $user_ip;
            $brows = $user_browser;
            $kets = $ket;
            $lats = $lat;
            $lons = $lon;

            $stmt->execute();
            return true;
        }

        $stmt->close();
    }
}



function berhasil_on($koneksi,$nis,$nama){
   $now = time();
       $hari_ini = date('Y-m-d H:i:s');
  $user_browser = $_SERVER['HTTP_USER_AGENT'];
            $user_ip = $_SERVER['REMOTE_ADDR'];
    $current_page =  $_SERVER['REQUEST_URI'];
  $user_status = '1';
  $user_remotes = ($user_ip);
$sql = "INSERT INTO akm_online (NIS,waktu,nama,aktif,client,timer,ip,browser) VALUES ('$nis',NOW(), '$nama', '$user_stat','$user_rem','$time','$ip_s', '$brows')";

if ($koneksi->query($sql) === TRUE) {
return true;
    echo "New record created successfully";
echo '<script>alert("Nomor Induk Salikin Salah !");</script>';
} else {
return false;
   // echo "Error: " . $sql . "<br>" . $conn->error;
}


}





/** Autocomplete **/

function auto_complt_salikin($koneksi, $var)
    {
    $data = array();
    $sql = "SELECT a.NIS, a.nama_lengkap, a.foto_thumb, a.foto, b.munassiq, c.keterangan as taklim, d.status_jabatan as jabatan FROM  akm_jamaah a LEFT JOIN akm_munassiq b ON (a.id_munassiq = b.id_munassiq) LEFT JOIN akm_taklim c ON (a.id_taklim = c.id_taklim) LEFT JOIN akm_jabatan d ON (a.id_jabatan = d.id_jabatan) WHERE a.NIS = $var OR a.nama_lengkap LIKE CONCAT('$var','%') ORDER BY a.NIS ASC";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
        return false;
        }
      else
        {
        $stmt->bind_param('s', $variable);
        $variable = $var;
        $stmt->execute();
        $result = $stmt->get_result(); //$result is of type mysqli_result
        $num_rows = $result->num_rows;
        while ($rows = $result->fetch_assoc())
            {
            $data[] = $rows;
            }
        }

    return $data;
    }

ini_set('display_errors', 0);
/** FUNGSI SHOW DATA PASSWORD JAMAAH **/

function rest_salikin_login($koneksi, $nis)
    {
    $data = array();
    if ($nis == '')
        {
        $met_s = 'is not null';
        }
      else
        {
        $met_s = '= ?';
        }

    $sql = "SELECT a.NIS,a.nama, a.passwords, a.rule, b.nama_lengkap, b.foto_thumb, b.foto, c.munassiq, d.keterangan as taklim, e.status_jabatan as jabatan FROM akm_login a LEFT JOIN akm_jamaah b ON (a.NIS = b.NIS) LEFT JOIN akm_munassiq c ON (b.id_munassiq = c.id_munassiq) LEFT JOIN akm_taklim d ON (b.id_taklim = d.id_taklim) LEFT JOIN akm_jabatan e ON (b.id_jabatan = e.id_jabatan) WHERE a.NIS $met_s";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
        return false;
        }
      else
        {
        $stmt->bind_param('s', $nis_s);
        $nis_s = $nis;

        // $rules = $rule;

        $stmt->execute();
        $result = $stmt->get_result(); //$result is of type mysqli_result
        $num_rows = $result->num_rows;
        while ($rows = $result->fetch_assoc())
            {
            $data[] = $rows;
            }
        }

    return $data;
    }

/**  FUNGSI UNTUK MEMBUAT PASSWORD JAMAAH  **/

function create_salikin_login($koneksi, $nis, $password, $rule)
    {
    $pass = md5($password);
    $sql = "INSERT INTO akm_login (NIS,passwords,rule) VALUES (?,?,?)";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
        return false;
        }
      else
        {
        $stmt->bind_param("ssi", $nis_s, $passw, $rules);
        $nis_s = $nis;
        $passw = $pass;
        $rules = $rule;
        $stmt->execute();
        return true;
        }

    $stmt->close();
    }

/* Fungsi simpan Password Salikin */

function add_pass_salikin($koneksi, $nis, $pass, $rule)
    {

    $user_status = '0';
    $sql = "INSERT INTO akm_login (NIS,passwords,rule,user_status) VALUES (?,?,?,?) ON DUPLICATE KEY 
UPDATE passwords =  '$pass' , rule = '$rule'";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
        return false;
        }
      else
        {
        $stmt->bind_param("ssss", $nis_s, $passw, $rules, $user_stat);
        $nis_s = $nis;
        $passw = $pass;
        $rules = $rule;
        $user_stat = $user_status;
        $stmt->execute();
        return true;
        }

    $stmt->close();
    }

// **  session start   **/

function sec_session_start()
    {
    $session_name = 'sec_session_nis'; // Tentukan nama sesi khusus
    $secure = SECURE;

    // Hal ini akan menghentikan JavaScript saat mencoba mengakses identitas sesi.

    $httponly = true;

    // Memaksa sesi-sesi untuk hanya menggunakan kuki.

    if (ini_set('session.use_only_cookies', 1) === FALSE)
        {
        header("Location:index.php/error.php?err=Could not initiate a safe session (ini_set)");
        exit();
        }

    // Mendapatkan param kuki saat ini.

    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

    // Menentukan nama sesi sesuai set di atas.

    session_name($session_name);
session_set_cookie_params(10800);

    session_start(); // Start the PHP session
    session_regenerate_id(true); // melakukan regenerasi sesi dan menghapus yang lama.
    }

// **  cek login salikin  **//
// ** Cek Password Salikin **//

function login_salikin($koneksi, $var_pass,$pages)
    {
      //$pass_admin = base64_encode($var_pass);
$pass_admin = $var_pass;
    $sql = "SELECT a.NIS, a.passwords, a.rule, a.user_status, b.nama_lengkap, b.id_taklim as taklim, b.foto_thumb FROM akm_login a LEFT JOIN akm_jamaah b ON (a.NIS = b.NIS) WHERE a.passwords = ? LIMIT 1";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('s', $pass_admin); // Bind "$nis" to parameter.
    $stmt->execute(); // Menjalankan perintah yang sudah disiapkan.
    $stmt->store_result();

    // mendapatkan variabel-variabel dari hasilnya.

    $stmt->bind_result($db_nis, $db_password, $db_rule, $db_status, $db_nama, $db_taklim,$db_foto_t);
    $stmt->fetch();
    if ($stmt->num_rows == 1)
        {
        if ($db_password == $pass_admin)
            {
            $user_browser = $_SERVER['HTTP_USER_AGENT'];
            $user_ip = $_SERVER['REMOTE_ADDR'];

            // Proteksi XSS karena kita mungkin mendapatkan nilai ini
            // $db_nis = preg_replace("/[^0-9]+/", "", $db_nis);

            $_SESSION['nis_s'] = $db_nis;

            // $db_rule = preg_replace("/[^0-9]+/", "", $db_rule);

            $_SESSION['rule'] = $db_rule;

            // $db_status = preg_replace("/[^0-9]+/", "", $db_status);

            $_SESSION['user_status'] = $db_status;

            // Proteksi XSS karena kita mungkin mendapatkan nilai ini
            // $db_nama = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $db_nama);

            $_SESSION['nama_s'] = $db_nama;

            // $db_taklim = preg_replace("/[^0-9]+/", "", $db_taklim);

            $_SESSION['taklim'] = $db_taklim;


            $_SESSION['foto_t'] = $db_foto_t;

            // $_SESSION['login_string'] = hash('sha512',
            //        $password . $user_browser);
            // Log masuk berhasil.
        
        $_SESSION['page'] = $pages;    
            return true;
            }
          else
            {
            return false;
            }
        }
      else
        {
        return false;
        }
    }

// *** jika berhasil login *** ///
function succes_login($koneksi,$nis){
      $hari_ini = date('Y-m-d H:i:s');
  $user_browser = $_SERVER['HTTP_USER_AGENT'];
           // $user_ip = get_client_ip_server();
		$user_ip =  $_SERVER['REMOTE_ADDR'];
    $current_page =  $_SERVER['REQUEST_URI'];
  $user_status = '0';
  $user_remotes = ($user_ip);
   
     $sql = "INSERT INTO akm_log_login (NIS,waktu_login,akses,ip,browser,client) VALUES (?,NOW(),?,?,?,?)";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
        return false;
        }
      else
        {
        $stmt->bind_param("sssss", $nis_s, $c_page, $ip_s, $brows,$client);
        $nis_s = $nis;
        $c_page = $current_page;
        $ip_s = $user_ip;
        $brows = $user_browser;
        $client = $user_remotes;
        $stmt->execute();
        return true;
        }

    $stmt->close();
}

function succes_logout($koneksi,$nis){
     $user_status = '0';
    $sql = "UPDATE akm_login SET user_status = '0' WHERE NIS = ? ";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
        return false;
        }
      else
        {
        $stmt->bind_param("s", $nis_s);
        $nis_s = $nis;
        $stmt->execute();
        return true;
        }

    $stmt->close();
}


function kill_online($koneksi,$nis,$ip){
  
    $sql = "DELETE FROM akm_online WHERE NIS = ? AND ip = ? AND DATE(waktu) = CURDATE()";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt)
        {
          
        return false;
        }
      else
        {
        $stmt->bind_param("ss", $nis_s,$ip_s);
        $nis_s = $nis;
        $ip_s = $ip;
        $stmt->execute();
   
        return true;
        }

    $stmt->close();
}





function get_browser_name($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    
    return 'Other';
}







function cek_online2($koneksi,$nis_s,$permanen)
{

$ip_salikin = get_client_ip_server();
$user_browser = $_SERVER['HTTP_USER_AGENT'];
if (!isset($nis_s)) {
    return false;
} else {
   // $nis_s = $_SESSION['nis_s'];
     $sql = "SELECT a.NIS,a.ip,a.browser FROM akm_online a WHERE a.NIS = ? AND DATE(a.waktu) = CURDATE()";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        return 'Gagal Menghubungkan Ke Server';
    } else {
        $stmt->bind_param('s', $nis_s);
        $stmt->execute(); // Menjalankan respon yang telah ditentukan.
        $stmt->store_result();
         $stmt->bind_result($db_nis, $db_ip, $db_browser);
            $stmt->fetch();
        if ($stmt->num_rows==0) {
            return 'Belum Login';  
        } elseif (($permanen!=='Y')) {
           $browser = get_browser_name($db_browser);
                $ex=explode(' ',$db_browser);
                $os = $ex[1].' '.$ex[2].' '.$ex[3].' '.$ex[4].' '.$ex[5];
                return $db_ip.'-'.$browser.'-'.$os.'-DUPLICATE';
        } elseif ($permanen=='Y') {
            return true;
        } else {
            return true;
        }
    }
}

}



function cek_online($koneksi,$nis_s,$permanen)
{

$ip_salikin = get_client_ip_server();
$user_browser = $_SERVER['HTTP_USER_AGENT'];
if (!isset($nis_s)) {
    return false;
} else {
   // $nis_s = $_SESSION['nis_s'];
     $sql = "SELECT a.NIS,a.ip,a.browser FROM akm_online a WHERE a.NIS = ? AND DATE(a.waktu) = CURDATE()";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        return 'Gagal Menghubungkan Ke Server';
    } else {
        $stmt->bind_param('s', $nis_s);
        $stmt->execute(); // Menjalankan respon yang telah ditentukan.
        $stmt->store_result();
         $stmt->bind_result($db_nis, $db_ip, $db_browser);
            $stmt->fetch();
        if ($stmt->num_rows==0) {
            return 'Belum Login';  
        } else {
            return true;
        }
    }
}

}








// ** cek_login_salikin **/

function login_check($koneksi, $rule_s,$pages)
    {

    // Memeriksa jika semua variabel sesi sudah benar

    if (isset($_SESSION['nis_s'], $_SESSION['nama_s'], $_SESSION['rule'], $_SESSION['user_status']))
        {
        $nis_s = $_SESSION['nis_s'];
        $rule = $_SESSION['rule'];
        $nama_s = $_SESSION['nama_s'];
        $taklim = $_SESSION['taklim'];
        $user_status = $_SESSION['user_status'];
    $page = $_SESSION['page'];

        // Mencatat ''string user-agent'' pengguna.

        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $user_ip = $_SERVER['REMOTE_ADDR'];
        if ($stmt = $koneksi->prepare("SELECT passwords,rule 
                                      FROM akm_login 
                                      WHERE NIS = ? LIMIT 1"))
            {

            // Bind "$nis_s" to parameter.

            $stmt->bind_param('s', $nis_s);
            $stmt->execute(); // Menjalankan respon yang telah ditentukan.
            $stmt->store_result();
            if ($stmt->num_rows == 1)
                {

                // Jika pengguna ditemukan, mencatat variabel-variabel hasil.

                $stmt->bind_result($db_pass, $db_rule);
                $stmt->fetch();

                // $login_check = hash('sha512', $password . $user_browser);

                if ($rule == $db_rule AND $rule <= $rule_s)
                    {
            
            if($pages == $page) {
                    // Logged In!!!!

                    return true;

            }else{
                return false;
            }


                    }
                  else
                    {

                    // Not logged in

                    return false;
                    }
                }
              else
                {

                // Not logged in

                return false;
                }
            }
          else
            {

            // Not logged in

            return false;
            }
        }
      else
        {

        // Not logged in

        return false;
        }
    }



function cek_izin_onlinemasa1($koneksi,$nis){



$sql = "SELECT a.NIS, a.nama_lengkap FROM akm_nis a WHERE a.NIS = ? LIMIT 1";

    
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('s', $nis); // Bind "$nis" to parameter.
    $stmt->execute(); // Menjalankan perintah yang sudah disiapkan.
    $stmt->store_result();

     // mendapatkan variabel-variabel dari hasilnya.

    $stmt->bind_result($db_nis, $db_nama);
    $stmt->fetch();

    if ($stmt->num_rows == 0){
        return false;
    } else {
                      
                $_SESSION['nis_s'] = $db_nis;
                $_SESSION['nama_s'] = $db_nama;
                $_SESSION['salikin'] = $_SESSION['nama_s'];
                $_SESSION['page'] = $pages;
                return true;
        }
    }




function cek_niss($koneksi, $nis)
{
    $limit = 1;
    $sql   = "SELECT NIS from akm_nis b where b.NIS = ? limit $limit";
    $stmt  = $koneksi->prepare($sql);
    if (!$stmt) {
        return false;
    } else {
        $stmt->bind_param('s', $nis);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}


?>
