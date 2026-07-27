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

if ($user_id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $contact_type = strtolower(trim($_POST['contact_type'] ?? 'keluarga'));
    $name = trim($_POST['name'] ?? '');
    $phone_raw = trim($_POST['phone_number'] ?? '');

    $phone_number = preg_replace('/[^0-9]/', '', $phone_raw);
    if (substr($phone_number, 0, 1) === '0') {
        $phone_number = '62' . substr($phone_number, 1);
    }

    if ($id && !empty($name) && !empty($phone_number)) {
        $stmt = $conn->prepare("UPDATE sos_contacts SET contact_type = ?, name = ?, phone_number = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $contact_type, $name, $phone_number, $id, $user_id);
        $stmt->execute();
    }
}

header("Location: index.php?sos_updated=1");
exit;