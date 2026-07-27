<?php
require_once __DIR__ . '/../config/database.php';

function calculateHealthScore($data) {
    global $conn;

    // 1. Hitung BMI terlebih dahulu jika ada weight & height
    $weight = (float)($data['weight'] ?? 0);
    $height = (float)($data['height'] ?? 0);
    $bmi = 0;

    if ($height > 0 && $weight > 0) {
        $heightInMeters = $height / 100;
        $bmi = round($weight / ($heightInMeters * $heightInMeters), 2);
    } else if (isset($data['bmi'])) {
        $bmi = (float)$data['bmi'];
    }

    // 2. Mapping parameter input ke parameter_name di tabel `health_rules`
    $parameters = [
        'Heart Rate' => (float)($data['heart_rate'] ?? 0),
        'Systolic'   => (float)($data['systolic'] ?? 0),
        'Diastolic'  => (float)($data['diastolic'] ?? 0),
        'SpO2'       => (float)($data['spo2'] ?? 0),
        'Hydration'  => (float)($data['hydration'] ?? 0),
        'Sleep'      => (float)($data['sleep_hours'] ?? $data['sleep'] ?? 0),
        'Steps'      => (float)($data['steps'] ?? 0),
        'BMI'        => $bmi
    ];

    $totalScore = 0;
    $validParameterCount = 0;
    $breakdownDetails = [];

    // 3. Loop setiap indikator medis dan cocokkan ke tabel health_rules
    foreach ($parameters as $paramName => $value) {
        // Skip jika nilai parameter kosong / 0 (kecuali untuk beberapa indikator spesifik)
        if ($value <= 0 && $paramName !== 'Steps' && $paramName !== 'Hydration') {
            continue;
        }

        $sql = "SELECT score, status, description 
                FROM health_rules 
                WHERE parameter_name = ? 
                  AND ? BETWEEN min_value AND max_value 
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sd", $paramName, $value);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $score = (int)$row['score'];
                $totalScore += $score;
                $validParameterCount++;

                $breakdownDetails[$paramName] = [
                    'value'       => $value,
                    'score'       => $score,
                    'status'      => $row['status'],
                    'description' => $row['description']
                ];
            }
            mysqli_stmt_close($stmt);
        }
    }

    // 4. Hitung Skor Rata-rata Akhir (Skala 0 - 100)
    $finalHealthScore = ($validParameterCount > 0) 
        ? (int)round($totalScore / $validParameterCount) 
        : 80; // Fallback jika tidak ada parameter terhitung

    return [
        'health_score' => $finalHealthScore,
        'bmi'          => $bmi,
        'breakdown'    => $breakdownDetails // Rincian skor per parameter untuk analisis
    ];
}
?>