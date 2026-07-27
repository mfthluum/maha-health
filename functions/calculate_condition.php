<?php
require_once __DIR__ . '/../config/database.php';

function calculateCondition($healthScore) {
    global $conn;

    $healthScore = (int)$healthScore;

    // Cari kondisi di DB yang skornya masuk rentang min_health_score s/d max_health_score
    $sql = "SELECT * FROM health_conditions 
            WHERE ? BETWEEN min_health_score AND max_health_score 
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $healthScore);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row; // Mengembalikan array lengkap (condition_name, risk_level, character_level, dll)
        }
    }

    // Fallback jika skor di luar rentang
    return [
        'condition_name'  => 'Kondisi Normal',
        'category'        => 'Normal',
        'description'     => 'Kondisi tubuh stabil.',
        'risk_level'      => 'Low',
        'character_level' => 1
    ];
}