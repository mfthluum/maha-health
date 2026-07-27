<?php

/*
|--------------------------------------------------------------------------
| MAHA Health
| Database Configuration
|--------------------------------------------------------------------------
*/

// Membaca variabel dari Environment Railway (jika tidak ada, fallback ke localhost)
$host     = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$database = getenv('DB_NAME') ?: "maha_health";
$port     = getenv('DB_PORT') ?: 3306;

// Membuat koneksi (termasuk parameter port untuk Aiven)
$conn = mysqli_connect($host, $username, $password, $database, (int)$port);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal : " . mysqli_connect_error());
}

// Mengatur timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Menggunakan charset UTF-8
mysqli_set_charset($conn, "utf8");
