<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Memulai sesi hanya jika belum ada sesi yang aktif
}

// Mengatur level error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi Database
$base_url = "http://" . $_SERVER['HTTP_HOST']."/ArsipS2/";
$config = [
    "host" => "localhost",
    "port" => "3306", // Port default MySQL
    "user" => "root", // Username MySQL Anda
    "password" => "", // Password MySQL Anda, jika ada
    "dbname" => "arsip2", // Nama database
];

// Membuat koneksi ke database MySQL
$conn = mysqli_connect($config["host"], $config["user"], $config["password"], $config["dbname"]);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Mendefinisikan URL dasar
define('url', $base_url);

// Fungsi debug untuk menampilkan informasi
function debug($tmp) {
    echo "<pre>";
    print_r((is_object($tmp) ? get_class_methods($tmp) : $tmp));
    die();
}

// Mengkonversi array ke string untuk keperluan SQL
function arrayToString($arr, $glue = ",", $separator = true) {
    $data = [];
    foreach ($arr as $index => $item) {
        if ($separator) {
            $data[] = $index . '="' . (stripslashes(htmlspecialchars($item))) . '"';
        } else {
            $data[] = $index . "=" . (stripslashes(htmlspecialchars($item)));
        }
    }
    return implode($glue, $data);
}

// Mengambil data dari database
function getData($select) {
    global $conn; // Menggunakan variabel global $conn
    $result = mysqli_query($conn, $select);
    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];
    } else {
        return [];
    }
}

// Mengubah format tanggal ke format Indonesia
function date_indo($date_format = 'l, j F Y | H:i', $timestamp = '', $suffix = '') {
    if (trim($timestamp) == '') {
        $timestamp = time();
    }
    $date_format = preg_replace("/S/", "", $date_format);
    $pattern = [
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
        'January', 'February', 'March', 'April', 'June', 'July', 'August', 'September', 'October',
        'November', 'December',
        'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
    ];
    $replace = [
        'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
        'Januari', 'Februari', 'Maret', 'April', 'Juni', 'Juli', 'Agustus', 'September',
        'Oktober', 'November', 'Desember',
        'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min',
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
    ];

    if (ctype_digit($timestamp)) {
        $date = date($date_format, $timestamp);
        return str_replace($pattern, $replace, $date) . " {$suffix}";
    } else {
        $date = str_replace($replace, $pattern, $timestamp);
        return date($date_format, strtotime($date)) . " {$suffix}";
    }
}

// Fungsi untuk melakukan operasi insert
function setInsert($table, $array) {
    global $conn; // Menggunakan variabel global $conn
    $columns = implode(", ", array_keys($array));
    $values  = implode("', '", array_values($array));
    $query = "INSERT INTO $table ($columns) VALUES ('$values')";
    return mysqli_query($conn, $query);
}

// Fungsi untuk melakukan operasi update
function setUpdate($table, $array, $filter) {
    global $conn; // Menggunakan variabel global $conn
    $update_data = [];
    foreach ($array as $column => $value) {
        $update_data[] = "$column = '" . mysqli_real_escape_string($conn, $value) . "'";
    }
    $update_data = implode(", ", $update_data);

    $filter_data = [];
    foreach ($filter as $column => $value) {
        $filter_data[] = "$column = '" . mysqli_real_escape_string($conn, $value) . "'";
    }
    $filter_data = implode(" AND ", $filter_data);

    $query = "UPDATE $table SET $update_data WHERE $filter_data";
    return mysqli_query($conn, $query);
}

// Fungsi untuk melakukan operasi delete
function setDelete($table, $filter) {
    global $conn; // Menggunakan variabel global $conn
    $filter_data = [];
    foreach ($filter as $column => $value) {
        $filter_data[] = "$column = '" . mysqli_real_escape_string($conn, $value) . "'";
    }
    $filter_data = implode(" AND ", $filter_data);

    $query = "DELETE FROM $table WHERE $filter_data";
    return mysqli_query($conn, $query);
}

// Fungsi untuk mengatur pesan flash
function setFlash($status, $message = "") {
    $_SESSION["flash"] = ["status" => $status, "message" => $message];
}

// Fungsi untuk upload file
function doUpload($file) {
    $namaFile = $file['name'];
    $namaSementara = $file['tmp_name'];
    $dirUpload = "upload/";
    $filename = time() . $namaFile;
    
    // Pindahkan file
    $terupload = move_uploaded_file($namaSementara, $dirUpload . $filename);

    return $terupload ? ["filename" => $filename, "error" => false] : ["message" => "diUpload (Lampiran)", "error" => true];
}

// Fungsi untuk melakukan redirect
function redirect($url) {
    echo "<script>
        window.location.href = '" . $url . "'
    </script>";
}

// Fungsi untuk memeriksa sesi
function checkSession() {
    if (empty($_SESSION["id"])) {
        redirect("logout.php");
    }
}

// Fungsi untuk memeriksa status login
function isLoggin() {
    if (!empty($_SESSION["id"])) {
        redirect("index.php");
    }
}
?>
