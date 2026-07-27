<?php
// Aktifkan error reporting sementara
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config/session.php";
require_once "../config/database.php";
require_once "../functions/calculate_score.php";
require_once "../functions/calculate_character.php";
require_once "../functions/calculate_condition.php";

// Pastikan request dikirim via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Cek session user
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

/* ==========================================================
   1. Ambil & Format Data Form
========================================================== */
$heart_rate  = (int) ($_POST['heart_rate'] ?? 0);
$systolic    = (int) ($_POST['systolic'] ?? 0);
$diastolic   = (int) ($_POST['diastolic'] ?? 0);
$spo2        = (int) ($_POST['spo2'] ?? 0);
$temperature = (float) str_replace(',', '.', $_POST['temperature'] ?? 0);
$hydration   = (int) ($_POST['hydration'] ?? 0);
$sleep_hours = (float) str_replace(',', '.', $_POST['sleep_hours'] ?? 0);
$steps       = (int) ($_POST['steps'] ?? 0);
$weight      = (float) str_replace(',', '.', $_POST['weight'] ?? 0);
$height      = (float) str_replace(',', '.', $_POST['height'] ?? 0);

/* ==========================================================
   2. Validasi Range (lebih longgar untuk kasus kritis)
========================================================== */
$errors = [];

// Detak Jantung
if ($heart_rate < 30 || $heart_rate > 250) {
    $errors[] = "Detak Jantung harus antara 30 - 250 BPM";
}

// Tekanan Darah Sistolik
if ($systolic < 50 || $systolic > 300) {
    $errors[] = "Tekanan Darah Sistolik harus antara 50 - 300 mmHg";
}

// Tekanan Darah Diastolik
if ($diastolic < 30 || $diastolic > 200) {
    $errors[] = "Tekanan Darah Diastolik harus antara 30 - 200 mmHg";
}

// SpO2
if ($spo2 < 50 || $spo2 > 100) {
    $errors[] = "SpO₂ harus antara 50 - 100%";
}

// Suhu Tubuh
if ($temperature < 30.0 || $temperature > 45.0) {
    $errors[] = "Suhu Tubuh harus antara 30.0 - 45.0 °C";
}

// Hidrasi
if ($hydration < 0 || $hydration > 100) {
    $errors[] = "Tingkat Hidrasi harus antara 0 - 100%";
}

// Durasi Tidur
if ($sleep_hours < 0 || $sleep_hours > 24) {
    $errors[] = "Durasi Tidur harus antara 0 - 24 jam";
}

// Langkah
if ($steps < 0) {
    $errors[] = "Jumlah langkah tidak boleh negatif";
}

// Berat Badan
if ($weight < 10 || $weight > 400) {
    $errors[] = "Berat Badan harus antara 10 - 400 kg";
}

// Tinggi Badan
if ($height < 50 || $height > 250) {
    $errors[] = "Tinggi Badan harus antara 50 - 250 cm";
}

// Jika ada error
if (!empty($errors)) {
    $_SESSION['health_errors'] = $errors;
    header("Location: index.php?open_modal=1");
    exit();
}
/* ==========================================================
   3. Update Profil User (Berat & Tinggi Badan)
========================================================== */
$update = $conn->prepare("UPDATE users SET weight = ?, height = ? WHERE id = ?");
if ($update) {
    $update->bind_param("ddi", $weight, $height, $user_id);
    $update->execute();
    $update->close();
}

/* ==========================================================
   4. Kalkulasi Sistem (Skor, BMI, Level Karakter)
========================================================== */
$data = [
    "heart_rate"  => $heart_rate,
    "systolic"    => $systolic,
    "diastolic"   => $diastolic,
    "spo2"        => $spo2,
    "temperature" => $temperature,
    "hydration"   => $hydration,
    "sleep_hours" => $sleep_hours,
    "steps"       => $steps,
    "weight"      => $weight,
    "height"      => $height
];

if (function_exists('calculateHealthScore')) {
    $result = calculateHealthScore($data);
    $healthScore = $result['health_score'] ?? 0;
    $bmi = $result['bmi'] ?? 0;
} else if (function_exists('calculate_score')) {
    $healthScore = calculate_score($data);
    $height_m = $height / 100;
    $bmi = ($height_m > 0) ? round($weight / ($height_m * $height_m), 2) : 0;
} else {
    $healthScore = 80;
    $height_m = $height / 100;
    $bmi = ($height_m > 0) ? round($weight / ($height_m * $height_m), 2) : 0;
}

// Character Level
if (function_exists('calculateCharacter')) {
    $charResult = calculateCharacter($healthScore);
    $characterLevel = is_array($charResult) ? ($charResult['character_level'] ?? 1) : (int)$charResult;
} else if (function_exists('calculate_character')) {
    $charResult = calculate_character($healthScore);
    $characterLevel = is_array($charResult) ? ($charResult['character_level'] ?? 1) : (int)$charResult;
} else {
    $characterLevel = 1;
}

/* ==========================================================
   5. Simpan Data Ke Tabel health_records
========================================================== */
$stmt = $conn->prepare("
    INSERT INTO health_records (
        user_id, heart_rate, systolic, diastolic, spo2,
        temperature, hydration, sleep_hours, steps, bmi,
        health_score, character_level
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Query Error: " . $conn->error);
}

$bindTypes = "iiiiidididii";
$stmt->bind_param(
    $bindTypes,
    $user_id,
    $heart_rate,
    $systolic,
    $diastolic,
    $spo2,
    $temperature,
    $hydration,
    $sleep_hours,
    $steps,
    $bmi,
    $healthScore,
    $characterLevel
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php?success=1");
    exit();
} else {
    die("Gagal Menyimpan Ke Database: " . $stmt->error);
}
?>