<?php
session_start();

// Tampilkan error jika ada masalah query/koneksi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Import koneksi database
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
} elseif (file_exists('../../config/database.php')) {
    require_once '../../config/database.php';
} else {
    require_once '../koneksi.php';
}

// 1. Deteksi ID User dari Session (Mencakup berbagai kemungkinan penamaan session)
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['id_user'] ?? $_SESSION['user']['id'] ?? null;

// Jika user belum login / session tidak ditemukan
if (!$user_id) {
    die("<script>alert('Error: Session Login tidak ditemukan! Silakan login ulang.'); window.location.href='index.php';</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Ambil & Format Data
    $contact_type = strtolower(trim($_POST['contact_type'] ?? 'keluarga')); // Paksa lowercase agar cocok dengan ENUM MySQL
    $name         = trim($_POST['name'] ?? '');
    $phone_raw    = trim($_POST['phone_number'] ?? '');

    // Format Nomor HP ke standar Indonesia (62)
    $phone_number = preg_replace('/[^0-9]/', '', $phone_raw);
    if (substr($phone_number, 0, 1) === '0') {
        $phone_number = '62' . substr($phone_number, 1);
    }

    if (!empty($name) && !empty($phone_number)) {
        
        // 3. Query Insert Direct dengan error handling
        $query = "INSERT INTO sos_contacts (user_id, contact_type, name, phone_number) VALUES ('$user_id', '$contact_type', '$name', '$phone_number')";
        
        if ($conn->query($query)) {
            // Berhasil simpan, redirect ke index
            header("Location: index.php?sos_success=1");
            exit;
        } else {
            // Jika gagal insert ke MySQL, tampilkan pesan errornya langsung di layar
            die("<strong>Gagal Menyimpan ke Database:</strong> " . $conn->error . "<br><br>Query: " . $query);
        }

    } else {
        die("<script>alert('Mohon isi nama dan nomor telepon dengan benar!'); window.history.back();</script>");
    }
} else {
    header("Location: index.php");
    exit;
}