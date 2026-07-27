<?php
// 1. Pengaturan Error & Konfigurasi Dasar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../config/session.php";
require_once "../config/database.php";

// 2. Verifikasi Autentikasi User
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// 3. Load Seluruh Engine Fungsi Medis Dinamis
if (file_exists("../functions/calculate_score.php")) require_once "../functions/calculate_score.php";
if (file_exists("../functions/calculate_condition.php")) require_once "../functions/calculate_condition.php";
if (file_exists("../functions/calculate_recommendation.php")) require_once "../functions/calculate_recommendation.php";
if (file_exists("../functions/calculate_character.php")) require_once "../functions/calculate_character.php";

$user_id   = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['fullname'] ?? 'Pengguna';

// 4. Query Data Rekam Medis Terbaru
$query = "SELECT * FROM health_records WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$latest_data = mysqli_fetch_assoc($result);

// 5. Olah Data
if ($latest_data) {
    $has_data = true;
    $healthScore = (int)($latest_data['health_score'] ?? 0);

    $condData = function_exists('calculateCondition') ? calculateCondition($healthScore) : [];
    $conditionName = $condData['condition_name'] ?? 'Kondisi Normal';
    $riskLevel     = $condData['risk_level'] ?? 'Low';
    $condDesc      = $condData['description'] ?? 'Tubuh dalam kondisi stabil.';

    $charData = function_exists('calculateCharacter') ? calculateCharacter($healthScore) : [];
    $levelName   = $charData['level_name'] ?? 'Level 1';
    $charImage   = $charData['image_name'] ?? 'ct_lv1_healthy.png';
    $expression  = $charData['expression'] ?? 'Normal';
    $charStatus  = $charData['status'] ?? 'Healthy';
    $healthColor = $charData['health_color'] ?? '#22C55E';
    $animation   = $charData['animation'] ?? 'bounce';
    $charDesc    = $charData['description'] ?? 'Kondisi tubuh sangat prima.';

    $recData = function_exists('calculateRecommendation') ? calculateRecommendation($conditionName) : [];
    $recTitle    = $recData['title'] ?? 'Rekomendasi Kesehatan';
    $recText     = $recData['recommendation'] ?? 'Jaga pola hidup sehat.';
    $recFood     = $recData['food_recommendation'] ?? ($recData['food'] ?? '-');
    $recExercise = $recData['exercise_recommendation'] ?? ($recData['exercise'] ?? '-');
    $recWater    = $recData['water_recommendation'] ?? ($recData['water'] ?? '-');
    $recSleep    = $recData['sleep_recommendation'] ?? ($recData['sleep'] ?? '-');
    $recPriority = $recData['priority'] ?? 'Low';

    $is_critical = (strtolower($riskLevel) === 'high' || strtolower($riskLevel) === 'critical' || $healthScore < 40);
} else {
    $has_data = false;
    $is_critical = false;
}

// Ambil daftar kontak SOS
$sos_contacts_list = [];
if (isset($user_id)) {
    $q_sos = $conn->query("SELECT * FROM sos_contacts WHERE user_id = '$user_id' ORDER BY id DESC");
    if ($q_sos) {
        while ($row = $q_sos->fetch_assoc()) {
            $sos_contacts_list[] = $row;
        }
    }
}

// Ambil pesan error validasi (jika ada)
$error_messages = $_SESSION['health_errors'] ?? [];
unset($_SESSION['health_errors']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MAHA Health</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes custom-bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
        @keyframes custom-shaking { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }
        @keyframes custom-breathing { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.06); } }
        @keyframes custom-pulse-glow { 0%, 100% { opacity: 0.15; transform: scale(0.95); } 50% { opacity: 0.35; transform: scale(1.05); } }
        .anim-bounce { animation: custom-bounce 2.5s infinite ease-in-out; }
        .anim-shaking { animation: custom-shaking 0.25s infinite ease-in-out; }
        .anim-slow_breathing { animation: custom-breathing 4s infinite ease-in-out; }
        .anim-idle { transform: none; }
        .anim-alarm { animation: custom-shaking 0.12s infinite ease-in-out; }
        .avatar-glow { animation: custom-pulse-glow 3s infinite ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-emerald-900 text-white flex flex-col justify-between shrink-0 border-r border-emerald-800 z-40">
        <div>
            <div class="h-16 flex items-center space-x-3 px-6 bg-emerald-950/50 border-b border-emerald-800/60">
                <img src="../assets/img/logo/logo.png" alt="MAHA Health Logo" class="h-8 w-auto object-contain">
                <span class="text-lg font-extrabold tracking-tight text-white">MAHA<span class="text-emerald-400">HEALTH</span></span>
            </div>

            <nav class="p-4 space-y-1.5">
                <a href="index.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-emerald-600 text-white font-semibold transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="chatbot.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/60 transition-all font-medium">
                    <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>AI ChatBot Medis</span>
                </a>
                <button onclick="openSosModal()" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/60 transition-all font-medium text-left">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Kontak SOS</span>
                    </div>
                    <span class="text-xs bg-red-500/20 text-red-300 px-2 py-0.5 rounded-full border border-red-500/30 font-bold">+Hubungi</span>
                </button>
            </nav>
        </div>

        <div class="p-4 border-t border-emerald-800/60 bg-emerald-950/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center font-bold text-white shrink-0">
                        <?= strtoupper(substr($user_name ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($user_name ?? 'User'); ?></p>
                        <p class="text-xs text-emerald-300 truncate">Pasien Aktif</p>
                    </div>
                </div>
                <a href="../auth/logout.php" class="p-2 text-emerald-300 hover:text-white hover:bg-emerald-800 rounded-lg transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto relative">

        <!-- HEADER -->
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-30 shadow-sm">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-slate-800">Ringkasan Kesehatan Virtual</h1>
                <p class="text-xs text-slate-500 mt-0.5 hidden sm:block">Pantau kondisi Digital Twin dan rekomendasi medis real-time</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="triggerEmergencySOS()" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v1.341C7.67 7.165 6 9.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/></svg>
                    <span class="hidden sm:inline">PINTASAN SOS</span>
                    <span class="sm:hidden">SOS</span>
                </button>
                <button onclick="openHealthModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Input Rekam Medis</span>
                    <span class="sm:hidden">Input</span>
                </button>
            </div>
        </header>

        <!-- DASHBOARD CONTENT -->
        <main id="dashboardContainer" class="p-6 lg:p-8 space-y-6 transition-all duration-500 <?= ($is_critical ?? false) ? 'blur-md pointer-events-none select-none' : ''; ?>">

            <?php if (isset($_GET['success'])): ?>
            <div class="mb-2 p-4 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-500/20 text-sm font-medium flex justify-between items-center border border-emerald-400">
                <div class="flex items-center space-x-2">
                    <span class="text-lg">🎉</span>
                    <span>Data rekam medis berhasil dicatat dan dianalisis otomatis!</span>
                </div>
                <button onclick="this.parentElement.remove();" class="text-white hover:text-emerald-200 font-bold text-xl px-2">&times;</button>
            </div>
            <?php endif; ?>

            <?php if ($has_data): ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- KIRI -->
                <div class="lg:col-span-7 space-y-6 flex flex-col justify-between">
                    <!-- Skor & Kondisi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Skor Kesehatan</span>
                                <span class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: <?= htmlspecialchars($healthColor); ?>;"></span>
                            </div>
                            <div class="my-4 text-center">
                                <div class="text-5xl sm:text-6xl font-black tracking-tight" style="color: <?= htmlspecialchars($healthColor); ?>;"><?= $healthScore; ?></div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Dari 100 Poin</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden p-0.5 border border-slate-100">
                                <div class="h-full rounded-full transition-all duration-1000" style="width: <?= $healthScore; ?>%; background-color: <?= htmlspecialchars($healthColor); ?>;"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kondisi Medis</span>
                                <span class="text-[10px] font-extrabold uppercase px-3 py-1 rounded-full bg-slate-100 text-slate-700"><?= htmlspecialchars($conditionName); ?></span>
                            </div>
                            <div class="my-auto">
                                <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3.5 rounded-2xl border border-slate-100"><?= htmlspecialchars($condDesc); ?></p>
                            </div>
                            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-2">
                                <span>Analisis Sistem:</span>
                                <span class="font-semibold text-emerald-600 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Terverifikasi Engine
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Metrik Vital -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2"><span>📊</span><span>Metrik Vital Terakhir</span></h3>
                            <button type="button" onclick="openHealthModal()" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 transition">+ Update Data</button>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Tekanan Darah</span>
                                <span class="text-sm font-extrabold text-slate-800 mt-0.5 block"><?= $latest_data['systolic']; ?>/<?= $latest_data['diastolic']; ?> <span class="text-[10px] text-slate-400 font-normal">mmHg</span></span>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Detak Jantung</span>
                                <span class="text-sm font-extrabold text-slate-800 mt-0.5 block"><?= $latest_data['heart_rate']; ?> <span class="text-[10px] text-slate-400 font-normal">bpm</span></span>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Saturasi SpO2</span>
                                <span class="text-sm font-extrabold text-slate-800 mt-0.5 block"><?= $latest_data['spo2']; ?> <span class="text-[10px] text-slate-400 font-normal">%</span></span>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Suhu Tubuh</span>
                                <span class="text-sm font-extrabold text-slate-800 mt-0.5 block"><?= $latest_data['temperature']; ?> <span class="text-[10px] text-slate-400 font-normal">°C</span></span>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Hidrasi / Tidur</span>
                                <span class="text-sm font-extrabold text-slate-800 mt-0.5 block"><?= $latest_data['hydration']; ?>% / <?= $latest_data['sleep_hours']; ?>j</span>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">BMI Tubuh</span>
                                <span class="text-sm font-extrabold text-slate-800 mt-0.5 block"><?= $latest_data['bmi']; ?></span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-[11px] text-slate-400">
                            <span>Waktu Pencatatan:</span>
                            <span class="font-semibold text-slate-600"><?= date('d M Y, H:i', strtotime($latest_data['created_at'])); ?> WIB</span>
                        </div>
                    </div>

                    <!-- Rekomendasi -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2"><span>💡</span><span>Rekomendasi Medis Personal</span></h3>
                            <span class="text-[10px] uppercase font-black px-3 py-1 rounded-full bg-amber-100 text-amber-800 tracking-wider">Prioritas: <?= htmlspecialchars($recPriority); ?></span>
                        </div>
                        <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100/80 mb-4">
                            <h4 class="text-xs font-bold text-emerald-900 mb-1"><?= htmlspecialchars($recTitle); ?></h4>
                            <p class="text-xs text-emerald-800 leading-relaxed"><?= htmlspecialchars($recText); ?></p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-3">
                                <span class="text-xl">🥗</span>
                                <div>
                                    <span class="font-bold text-slate-800 block text-[11px]">Nutrisi & Makanan</span>
                                    <span class="text-slate-600 text-[11px]"><?= htmlspecialchars($recFood); ?></span>
                                </div>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-3">
                                <span class="text-xl">🏃</span>
                                <div>
                                    <span class="font-bold text-slate-800 block text-[11px]">Olahraga & Latihan</span>
                                    <span class="text-slate-600 text-[11px]"><?= htmlspecialchars($recExercise); ?></span>
                                </div>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-3">
                                <span class="text-xl">💧</span>
                                <div>
                                    <span class="font-bold text-slate-800 block text-[11px]">Konsumsi Air</span>
                                    <span class="text-slate-600 text-[11px]"><?= htmlspecialchars($recWater); ?></span>
                                </div>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-start space-x-3">
                                <span class="text-xl">😴</span>
                                <div>
                                    <span class="font-bold text-slate-800 block text-[11px]">Pola Istirahat</span>
                                    <span class="text-slate-600 text-[11px]"><?= htmlspecialchars($recSleep); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Digital Twin -->
                <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 shadow-md border border-slate-200/80 relative overflow-hidden flex flex-col justify-between items-center text-center h-full min-h-[580px]">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 rounded-full blur-3xl avatar-glow pointer-events-none" style="background-color: <?= htmlspecialchars($healthColor); ?>;"></div>

                    <div class="w-full flex justify-between items-center relative z-10 mb-2">
                        <span class="text-xs font-black uppercase tracking-widest bg-slate-100 text-slate-600 px-3.5 py-1.5 rounded-xl border border-slate-200/60"><?= htmlspecialchars($levelName); ?></span>
                        <span class="text-xs font-black uppercase tracking-wider px-3.5 py-1.5 rounded-xl text-white shadow-md" style="background-color: <?= htmlspecialchars($healthColor); ?>;"><?= htmlspecialchars($charStatus); ?></span>
                    </div>

                    <div class="my-auto relative z-10 flex flex-col items-center justify-center w-full h-full">
                        <div class="relative w-full h-[400px] sm:h-[460px] flex items-center justify-center py-2">
                            <?php
                            $rawImage = $charData['image_name'] ?? 'dt_lv1_healthy.png';
                            $fixedImage = str_replace('ct_', 'dt_', $rawImage);
                            ?>
                            <img src="../assets/img/digital_twin/<?= htmlspecialchars($fixedImage); ?>"
                                 alt="<?= htmlspecialchars($expression); ?>"
                                 class="h-full w-full object-contain filter drop-shadow-2xl transition-all duration-300 transform scale-105 anim-<?= htmlspecialchars($animation); ?>"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/320?text=Avatar+Twin';">
                        </div>
                        <div class="mt-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">Status Digital Twin</span>
                            <h3 class="text-2xl font-extrabold text-slate-900 mt-2">"<?= htmlspecialchars($expression); ?>"</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed"><?= htmlspecialchars($charDesc); ?></p>
                        </div>
                    </div>

                    <div class="w-full relative z-10 bg-slate-50 rounded-2xl p-4 border border-slate-100 flex justify-between items-center text-xs mt-4">
                        <span class="text-slate-500 font-semibold">Tingkat Risiko Tubuh:</span>
                        <span class="font-extrabold px-3.5 py-1 rounded-xl text-white shadow-sm" style="background-color: <?= htmlspecialchars($healthColor); ?>;"><?= htmlspecialchars($riskLevel); ?></span>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-12 text-center max-w-lg mx-auto my-12">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-4 text-3xl font-extrabold shadow-inner">+</div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Rekam Kesehatan</h3>
                <p class="text-xs text-slate-500 mb-6 leading-relaxed">Mulai catat tekanan darah, detak jantung, dan parameter fisik Anda untuk mengaktifkan Digital Twin.</p>
                <button type="button" onclick="openHealthModal()" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3.5 rounded-2xl text-xs transition shadow-lg shadow-emerald-600/20">Input Data Pertama Anda</button>
            </div>
            <?php endif; ?>
        </main>

        <!-- OVERLAY KRITIS -->
        <?php if ($is_critical ?? false): ?>
        <div id="criticalOverlay" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-white max-w-lg w-full rounded-3xl p-8 shadow-2xl text-center border-4 border-red-500 relative">
                <button onclick="closeCriticalOverlay()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-slate-100 hover:bg-red-100 text-slate-500 hover:text-red-600 flex items-center justify-center font-bold text-lg">&times;</button>
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="inline-block bg-red-600 text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-widest mb-2">Peringatan Darurat Medis</span>
                <h2 class="text-2xl font-black text-slate-900 mb-2">KONDISI KRITIS TERDETEKSI!</h2>
                <p class="text-slate-600 text-sm mb-6">Sistem mendeteksi parameter vital Anda berada pada rentang yang membahayakan.</p>
                <button onclick="triggerEmergencySOS()" class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold text-lg py-5 rounded-2xl shadow-xl shadow-red-600/40 flex items-center justify-center space-x-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v1.341C7.67 7.165 6 9.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/></svg>
                    <span>KIRIM SEKARANG PANGGILAN SOS</span>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- MODAL INPUT REKAM MEDIS -->
    <div id="healthModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="healthModalCard">
            <div class="bg-emerald-600 text-white p-5 sm:p-6 flex items-center justify-between border-b border-emerald-500">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl">📋</div>
                    <div>
                        <h3 class="font-extrabold text-base sm:text-lg">Catat Rekam Medis & Fisik</h3>
                        <p class="text-emerald-100 text-xs mt-0.5">Pembaruan skor kesehatan & Digital Twin Anda</p>
                    </div>
                </div>
                <button type="button" onclick="closeHealthModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5">
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 text-slate-500 text-xs flex items-start space-x-2">
                    <span class="text-base">💡</span>
                    <span><strong>Petunjuk:</strong> Masukkan parameter kondisi tubuh Anda secara akurat.</span>
                </div>

                <form action="save_health.php" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">❤️ Detak Jantung (BPM)</label>
                            <input type="number" name="heart_rate" required placeholder="Contoh: 80" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">🩸 Tekanan Darah Sistolik</label>
                            <input type="number" name="systolic" required placeholder="Contoh: 120" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">🩸 Tekanan Darah Diastolik</label>
                            <input type="number" name="diastolic" required placeholder="Contoh: 80" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">🫁 SpO₂ (%)</label>
                            <input type="number" name="spo2" required placeholder="Contoh: 98" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">🌡 Suhu Tubuh (°C)</label>
                            <input type="number" step="0.1" name="temperature" required placeholder="Contoh: 36.7" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">💧 Hidrasi (%)</label>
                            <input type="number" name="hydration" required placeholder="Contoh: 85" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">😴 Durasi Tidur (Jam)</label>
                            <input type="number" step="0.1" name="sleep_hours" required placeholder="Contoh: 8" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">🚶 Langkah Kaki</label>
                            <input type="number" name="steps" required placeholder="Contoh: 10000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">⚖ Berat Badan (KG)</label>
                            <input type="number" step="0.1" name="weight" required value="62.00" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">📏 Tinggi Badan (CM)</label>
                            <input type="number" step="0.1" name="height" required value="170.00" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
                        <button type="button" onclick="closeHealthModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md shadow-emerald-600/20">💾 Simpan Data & Analisis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SOS -->
    <div id="sosModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white max-w-lg w-full rounded-2xl p-6 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Kelola Kontak Darurat SOS</h3>
                    <p class="text-xs text-slate-500">Tambah, lihat, edit, atau hapus nomor penerima sinyal SOS</p>
                </div>
                <button onclick="closeSosModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl">✕</button>
            </div>
            <div class="overflow-y-auto space-y-6 pt-4 pr-1">
                <form id="sosForm" action="save_sos_contact.php" method="POST" class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                    <input type="hidden" id="sos_id" name="id" value="">
                    <div class="flex justify-between items-center">
                        <span id="formTitle" class="text-xs font-extrabold uppercase tracking-wider text-emerald-700">+ Tambah Kontak Baru</span>
                        <button type="button" id="btnCancelEdit" onclick="resetSosForm()" class="hidden text-xs text-red-500 hover:underline">Batal Edit</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Kategori Kontak</label>
                            <select id="sos_type" name="contact_type" required class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500 bg-white">
                                <option value="keluarga">Keluarga</option>
                                <option value="rumah_sakit">Rumah Sakit</option>
                                <option value="ambulans">Layanan Ambulans</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Nama / Instansi</label>
                            <input type="text" id="sos_name" name="name" required placeholder="Contoh: Ayah / RS Medika" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nomor WhatsApp (08 / 62)</label>
                        <input type="text" id="sos_phone" name="phone_number" required placeholder="085716541442" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                    </div>
                    <button type="submit" id="btnSubmitSos" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-xs">Simpan Kontak SOS</button>
                </form>

                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kontak Tersimpan (<?= count($sos_contacts_list); ?>)</h4>
                    <?php if (empty($sos_contacts_list)): ?>
                        <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-400">Belum ada nomor darurat tersimpan.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            <?php foreach ($sos_contacts_list as $contact): ?>
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-200 shadow-sm">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs uppercase
                                            <?= $contact['contact_type'] == 'keluarga' ? 'bg-blue-100 text-blue-700' : ($contact['contact_type'] == 'rumah_sakit' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'); ?>">
                                            <?= substr($contact['contact_type'], 0, 1); ?>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($contact['name']); ?></p>
                                            <p class="text-[11px] text-slate-500"><?= htmlspecialchars($contact['phone_number']); ?> • <?= str_replace('_', ' ', $contact['contact_type']); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <button type="button" onclick="editSosContact(<?= htmlspecialchars(json_encode($contact)); ?>)" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <a href="delete_sos_contact.php?id=<?= $contact['id']; ?>" onclick="return confirm('Yakin ingin menghapus kontak ini?')" class="p-1.5 text-slate-500 hover:text-red-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto buka modal + tampilkan error validasi (jika ada)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Auto buka modal
            if (urlParams.get('open_modal') === '1') {
                openHealthModal();
            }

            // Tampilkan error validasi dengan SweetAlert menarik
            <?php if (!empty($error_messages)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Data Input Tidak Sesuai',
                html: `<div class="text-left text-sm space-y-1">
                    <p class="mb-2 text-slate-600">Mohon perbaiki data berikut:</p>
                    <ul class="list-disc pl-5 text-red-600">
                        <?php foreach ($error_messages as $err): ?>
                            <li><?= htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>`,
                confirmButtonText: 'Perbaiki Sekarang',
                confirmButtonColor: '#059669',
                allowOutsideClick: false
            }).then(() => {
                openHealthModal();
            });
            <?php endif; ?>
        });

        // Modal Health
        function openHealthModal() {
            const modal = document.getElementById('healthModal');
            const modalCard = document.getElementById('healthModalCard');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalCard.classList.remove('scale-95', 'opacity-0');
                modalCard.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeHealthModal() {
            const modal = document.getElementById('healthModal');
            const modalCard = document.getElementById('healthModalCard');
            modalCard.classList.remove('scale-100', 'opacity-100');
            modalCard.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 200);
        }

        document.getElementById('healthModal').addEventListener('click', function(e) {
            if (e.target === this) closeHealthModal();
        });

        // Modal SOS
        function openSosModal() {
            document.getElementById('sosModal').classList.remove('hidden');
        }

        function closeSosModal() {
            document.getElementById('sosModal').classList.add('hidden');
            resetSosForm();
        }

        function editSosContact(contact) {
            document.getElementById('sosForm').action = 'update_sos_contact.php';
            document.getElementById('sos_id').value = contact.id;
            document.getElementById('sos_type').value = contact.contact_type;
            document.getElementById('sos_name').value = contact.name;
            document.getElementById('sos_phone').value = contact.phone_number;
            document.getElementById('formTitle').innerText = '✏️ Edit Kontak SOS';
            document.getElementById('btnSubmitSos').innerText = 'Update Kontak SOS';
            document.getElementById('btnSubmitSos').classList.replace('bg-emerald-600', 'bg-amber-600');
            document.getElementById('btnSubmitSos').classList.replace('hover:bg-emerald-700', 'hover:bg-amber-700');
            document.getElementById('btnCancelEdit').classList.remove('hidden');
        }

        function resetSosForm() {
            document.getElementById('sosForm').action = 'save_sos_contact.php';
            document.getElementById('sos_id').value = '';
            document.getElementById('sosForm').reset();
            document.getElementById('formTitle').innerText = '+ Tambah Kontak Baru';
            document.getElementById('btnSubmitSos').innerText = 'Simpan Kontak SOS';
            document.getElementById('btnSubmitSos').classList.replace('bg-amber-600', 'bg-emerald-600');
            document.getElementById('btnSubmitSos').classList.replace('hover:bg-amber-700', 'hover:bg-emerald-700');
            document.getElementById('btnCancelEdit').classList.add('hidden');
        }

        // Tutup Overlay Kritis
        function closeCriticalOverlay() {
            const overlay = document.getElementById('criticalOverlay');
            if (overlay) overlay.style.display = 'none';
            const dashboard = document.getElementById('dashboardContainer');
            if (dashboard) {
                dashboard.classList.remove('blur-md', 'pointer-events-none', 'select-none');
            }
        }

        // Fungsi SOS
        function triggerEmergencySOS() {
            Swal.fire({
                title: 'Mendeteksi Lokasi GPS...',
                text: 'Mohon izinkan akses lokasi di browser Anda.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            if (!("geolocation" in navigator)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Browser Tidak Mendukung GPS',
                    text: 'Tetap kirim SOS tanpa lokasi?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim Tanpa Lokasi',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626'
                }).then((result) => {
                    if (result.isConfirmed) sendSosRequest('Tidak diketahui', 'Tidak diketahui');
                });
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    sendSosRequest(position.coords.latitude, position.coords.longitude);
                },
                function (error) {
                    let errorMsg = "Posisi GPS tidak ditemukan.";
                    if (error.code === 1) errorMsg = "Izin lokasi ditolak.";
                    if (error.code === 2) errorMsg = "Posisi GPS tidak tersedia.";
                    if (error.code === 3) errorMsg = "Waktu pencarian GPS habis.";

                    Swal.fire({
                        icon: 'warning',
                        title: 'GPS Tidak Tersedia',
                        text: errorMsg + ' Tetap kirim SOS tanpa lokasi?',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Kirim Tanpa Lokasi',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc2626'
                    }).then((result) => {
                        if (result.isConfirmed) sendSosRequest('Tidak diketahui', 'Tidak diketahui');
                    });
                },
                options
            );
        }

        function sendSosRequest(lat, lng) {
            const formData = new FormData();
            formData.append('latitude', lat);
            formData.append('longitude', lng);

            fetch('send_sos.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sinyal SOS Terkirim!',
                        text: data.message,
                        confirmButtonColor: '#059669'
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: data.message,
                        confirmButtonText: '+ Kelola Kontak SOS',
                        confirmButtonColor: '#dc2626'
                    }).then((result) => {
                        if (result.isConfirmed) openSosModal();
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim SOS',
                    text: 'Terjadi kesalahan pada server atau koneksi.',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    </script>
</body>
</html>