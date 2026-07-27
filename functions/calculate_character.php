<?php
require_once __DIR__ . '/../config/database.php';

function calculateCharacter($healthScore) {
    global $conn;

    $healthScore = (int)$healthScore;

    // Cari karakter yang cocok dengan skor kesehatan
    $sql = "SELECT * FROM character_levels 
            WHERE ? BETWEEN min_score AND max_score 
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $healthScore);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row; // Mengembalikan array lengkap: image_name, expression, health_color, animation, dll.
        }
    }

    // Default fallback jika skor tidak terjangkau
    return [
        'level_name'   => 'Level 1',
        'image_name'   => 'ct_lv1_healthy.png',
        'expression'   => 'Senyum Ceria',
        'status'       => 'Healthy',
        'health_color' => '#22C55E',
        'animation'    => 'bounce',
        'description'  => 'Tubuh dalam kondisi sehat.'
    ];
}