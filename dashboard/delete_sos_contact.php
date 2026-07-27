<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
} elseif (file_exists('../../config/database.php')) {
    require_once '../../config/database.php';
} else {
    require_once '../koneksi.php';
}

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['id_user'] ?? $_SESSION['user']['id'] ?? null;
$id = $_GET['id'] ?? null;

if ($user_id && $id) {
    // Keamanan: Hapus kontak HANYA jika id dan user_id cocok
    $stmt = $conn->prepare("DELETE FROM sos_contacts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

header("Location: index.php?sos_deleted=1");
exit;