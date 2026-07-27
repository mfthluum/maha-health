<?php

/**
 * ==========================================
 * MAHA HEALTH
 * Helper Functions
 * ==========================================
 */


/**
 * Membersihkan input user
 */
function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}


/**
 * Redirect halaman
 */
function redirect($url)
{
    header("Location: $url");
    exit();
}


/**
 * Menampilkan alert javascript
 */
function alert($message)
{
    echo "<script>alert('$message');</script>";
}


/**
 * Alert lalu redirect
 */
function alertRedirect($message, $url)
{
    echo "
    <script>

        alert('$message');

        window.location.href='$url';

    </script>";
    exit();
}


/**
 * Format angka Health Score
 */
function formatScore($score)
{
    return number_format($score, 0);
}


/**
 * Menghitung BMI
 */
function calculateBMI($weight, $height)
{
    if ($height <= 0) {
        return 0;
    }

    $height = $height / 100;

    return round($weight / ($height * $height), 1);
}


/**
 * Kategori BMI
 */
function bmiCategory($bmi)
{
    if ($bmi < 18.5)
        return "Underweight";

    if ($bmi < 25)
        return "Normal";

    if ($bmi < 30)
        return "Overweight";

    return "Obese";
}


/**
 * Format tanggal Indonesia
 */
function formatDate($date)
{
    return date("d M Y", strtotime($date));
}


/**
 * Format waktu Indonesia
 */
function formatDateTime($datetime)
{
    return date("d M Y H:i", strtotime($datetime));
}


/**
 * Greeting berdasarkan waktu
 */
function greeting()
{

    $hour = date("H");

    if ($hour < 11)
        return "Selamat Pagi";

    if ($hour < 15)
        return "Selamat Siang";

    if ($hour < 18)
        return "Selamat Sore";

    return "Selamat Malam";
}


/**
 * Status Health Score
 */
function healthStatus($score)
{

    if ($score >= 90)
        return "Excellent";

    if ($score >= 75)
        return "Good";

    if ($score >= 60)
        return "Fair";

    if ($score >= 40)
        return "Warning";

    return "Critical";
}


/**
 * Badge Bootstrap berdasarkan status
 */
function badgeColor($status)
{

    switch ($status) {

        case "Excellent":
            return "success";

        case "Good":
            return "primary";

        case "Fair":
            return "warning";

        case "Warning":
            return "danger";

        default:
            return "dark";
    }
}


/**
 * Warna Progress Bar
 */
function progressColor($score)
{

    if ($score >= 90)
        return "bg-success";

    if ($score >= 75)
        return "bg-primary";

    if ($score >= 60)
        return "bg-warning";

    return "bg-danger";
}


/**
 * Generate Avatar Huruf
 */
function avatarLetter($name)
{
    return strtoupper(substr($name, 0, 1));
}

?>