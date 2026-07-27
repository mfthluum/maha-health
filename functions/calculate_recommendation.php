<?php
require_once __DIR__ . '/../config/database.php';

function calculateRecommendation($conditionName) {
    global $conn;

    // Jika yang masuk adalah array, ambil nama kondisinya
    if (is_array($conditionName)) {
        $conditionName = $conditionName['condition_name'] ?? 'Sehat';
    }

    $sql = "SELECT * FROM health_recommendations 
            WHERE condition_name = ? 
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $conditionName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row; // Mengembalikan array data dari tabel health_recommendations
        }
    }

    return [
        'title'          => 'Tidak ada rekomendasi',
        'recommendation' => 'Belum tersedia.',
        'food_recommendation'     => '-',
        'exercise_recommendation' => '-',
        'water_recommendation'    => '-',
        'sleep_recommendation'   => '-',
        'priority'       => 'Low'
    ];
}