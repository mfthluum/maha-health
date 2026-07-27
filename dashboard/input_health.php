<?php

require_once "../config/session.php";
require_once "../config/database.php";

$userId = $_SESSION['user']['id'];
$userName = $_SESSION['user']['fullname'];

// Ambil data pengguna terbaru dari database
$stmt = $conn->prepare("
SELECT
fullname,
height,
weight
FROM users
WHERE id = ?
LIMIT 1
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$height = $userData['height'];
$weight = $userData['weight'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Input Data Kesehatan | MAHA Health</title>

    <link rel="icon" href="../assets/img/logo/logo.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- Tailwind CSS CDN & Fonts untuk styling overlay -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a; /* Dark background untuk efek blur maksimal */
        }

        /* Overlay Mengambang dengan Blur Background */
        .modal-blur-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65); /* Overlay Slate Gelap Transparan */
            backdrop-filter: blur(12px); /* Blur Efek di Belakang Jendela */
            -webkit-backdrop-filter: blur(12px);
            z-index: 9999;
            overflow-y: auto;
        }

        .floating-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }
    </style>

</head>

<body class="bg-slate-900 min-h-screen">

    <!-- OVERLAY NGAMBANG + BLUR BACKGROUND -->
    <div class="modal-blur-overlay flex items-center justify-center p-4 sm:p-6 md:p-10">

        <!-- CONTAINER JENDELA NGAMBANG (FLOATING WINDOW) -->
        <div class="floating-card max-w-3xl w-full relative overflow-hidden border border-slate-100 my-auto animate-fadeIn">

            <!-- HEADER CARD WITH CLOSE BUTTON (X) -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white px-6 py-5 flex items-center justify-between relative">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl">
                        📋
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold tracking-tight mb-0 text-white">
                            Catat Rekam Medis & Fisik
                        </h3>
                        <p class="text-emerald-100 text-xs mb-0">
                            Pembaruan skor kesehatan & Digital Twin Anda
                        </p>
                    </div>
                </div>

                <!-- TOMBOL CLOSE (X) UNTUK BATAL INPUT -->
                <a href="index.php" 
                   class="w-9 h-9 rounded-full bg-black/10 hover:bg-black/30 text-white flex items-center justify-center transition duration-200 no-underline text-lg font-bold"
                   title="Batal / Tutup">
                    ✕
                </a>
            </div>

            <!-- BODY FORM -->
            <div class="p-6 sm:p-8 max-h-[80vh] overflow-y-auto">

                <p class="text-slate-500 text-xs sm:text-sm mb-6 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80">
                    💡 <strong>Petunjuk:</strong> Masukkan parameter kondisi tubuh Anda di bawah ini secara akurat untuk memperbarui skor kesehatan dan tampilan Digital Twin Anda.
                </p>

                <form action="save_health.php" method="POST">

                    <div class="row">

                        <!-- Heart Rate / Detak Jantung -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                ❤️ Detak Jantung (BPM)
                            </label>
                            <input
                                type="number"
                                name="heart_rate"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 80"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Jumlah denyut nadi per menit.</small>
                        </div>

                        <!-- Systolic / Sistolik -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                🩸 Tekanan Darah Sistolik (mmHg)
                            </label>
                            <input
                                type="number"
                                name="systolic"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 120"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Angka atas pada hasil tensi.</small>
                        </div>

                        <!-- Diastolic / Diastolik -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                🩸 Tekanan Darah Diastolik (mmHg)
                            </label>
                            <input
                                type="number"
                                name="diastolic"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 80"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Angka bawah pada hasil tensi.</small>
                        </div>

                        <!-- SpO2 / Kadar Oksigen -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                🫁 Kadar Oksigen Darah / SpO₂ (%)
                            </label>
                            <input
                                type="number"
                                name="spo2"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 98"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Saturasi oksigen dalam darah.</small>
                        </div>

                        <!-- Temperature / Suhu Tubuh -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                🌡 Suhu Tubuh (°C)
                            </label>
                            <input
                                type="number"
                                step="0.1"
                                name="temperature"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 36.7"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Suhu tubuh normal sekitar 36.5 - 37.5 °C.</small>
                        </div>

                        <!-- Hydration / Level Hidrasi -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                💧 Tingkat Hidrasi / Cairan (%)
                            </label>
                            <input
                                type="number"
                                name="hydration"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 85"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Estimasi kecukupan asupan air minum harian.</small>
                        </div>

                        <!-- Sleep / Durasi Tidur -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                😴 Durasi Tidur (Jam)
                            </label>
                            <input
                                type="number"
                                step="0.1"
                                name="sleep_hours"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 8"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Lama waktu tidur Anda semalam.</small>
                        </div>

                        <!-- Steps / Langkah Kaki -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                🚶 Aktivitas Langkah Kaki
                            </label>
                            <input
                                type="number"
                                name="steps"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 10000"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">Jumlah langkah kaki Anda hari ini.</small>
                        </div>

                        <!-- Weight / Berat Badan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                ⚖ Berat Badan (Kg)
                            </label>
                            <input
                                type="number"
                                step="0.1"
                                name="weight"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                value="<?= htmlspecialchars($weight); ?>"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">
                                Diambil otomatis dari profil. Bisa disesuaikan jika berat badan berubah.
                            </small>
                        </div>

                        <!-- Height / Tinggi Badan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold uppercase text-slate-700">
                                📏 Tinggi Badan (Cm)
                            </label>
                            <input
                                type="number"
                                step="0.1"
                                name="height"
                                class="form-control rounded-xl py-2.5 text-sm border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                value="<?= htmlspecialchars($height); ?>"
                                required>
                            <small class="text-slate-400 text-[11px] block mt-1">
                                Diambil otomatis dari profil. Sesuaikan jika diperlukan.
                            </small>
                        </div>

                    </div>

                    <hr class="my-4 border-slate-100">

                    <!-- ACTION BUTTONS -->
                    <div class="flex items-center justify-end space-x-3">
                        <a href="index.php" class="btn btn-light rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-600 border border-slate-200 hover:bg-slate-100">
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold px-6 py-2.5 rounded-xl text-xs sm:text-sm transition duration-200 shadow-lg shadow-emerald-600/25">
                            💾 Simpan Data & Analisis
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>