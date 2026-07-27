<?php

/*
|--------------------------------------------------------------------------
| MAHA Health
| Database Configuration
|--------------------------------------------------------------------------
*/

$host = "localhost";
$username = "root";
$password = "";
$database = "maha_health";

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal : " . mysqli_connect_error());
}

// Mengatur timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Menggunakan charset UTF-8
mysqli_set_charset($conn, "utf8");