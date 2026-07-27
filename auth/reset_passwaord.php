<?php
session_start();

// Matikan error display agar tampilan tetap bersih
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Sertakan Koneksi Database Fleksibel
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
} elseif (file_exists('../../config/database.php')) {
    require_once '../../config/database.php';
} elseif (file_exists('../koneksi.php')) {
    require_once '../koneksi.php';
} else {
    die("Koneksi database tidak ditemukan.");
}

$token = $_GET['token'] ?? '';
$valid_token = false;
$user_id = null;

if (!empty($token)) {
    // Cek apakah token valid dan belum kadaluwarsa
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $valid_token = true;
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | MAHA Health</title>

    <link rel="icon" href="../assets/img/logo/logo.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    
    <!-- Google Fonts & Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a; /* Slate 900 */
        }
        
        /* Glassmorphism & Background Dynamic Glows */
        .auth-bg-glow-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.25) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-bg-glow-2 {
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(5,150,105,0.2) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        @keyframes custom-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float {
            animation: custom-float 4s infinite ease-in-out;
        }
    </style>
</head>

<body class="min-h-screen relative overflow-x-hidden flex items-center justify-center p-4 lg:p-8">

    <div class="auth-bg-glow-1"></div>
    <div class="auth-bg-glow-2"></div>

    <div class="container-fluid max-w-6xl relative z-10">

        <div class="row auth-container overflow-hidden rounded-3xl glass-card border-0">

            <!-- ================= LEFT PANEL ================= -->
            <div class="col-lg-6 left-panel bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                
                <div class="absolute inset-0 bg-black/10 pointer-events-none"></div>

                <div class="my-auto py-6 relative z-10 text-center flex flex-col items-center">
                    
                    <div class="relative mb-8 flex items-center justify-center">
                        <div class="absolute w-48 h-48 lg:w-56 lg:h-56 bg-emerald-300/40 rounded-full blur-3xl animate-pulse"></div>
                        
                        <div class="w-36 h-36 lg:w-48 lg:h-48 bg-white/15 backdrop-blur-lg rounded-3xl p-6 border-2 border-white/30 shadow-2xl flex items-center justify-center transform transition duration-500 hover:scale-105 hover:rotate-1">
                            <img src="../assets/img/logo/logo.png" 
                                 alt="MAHA Health Logo" 
                                 class="w-full h-full object-contain filter drop-shadow-2xl"
                                 onerror="this.src='https://via.placeholder.com/200?text=MAHA';">
                        </div>
                    </div>

                    <h1 class="text-3xl lg:text-4xl font-black tracking-wider text-white">
                        MAHA<span class="text-emerald-300">HEALTH</span>
                    </h1>

                    <div class="inline-flex items-center space-x-2 bg-emerald-500/30 border border-emerald-300/30 px-4 py-1.5 rounded-full text-xs font-bold text-emerald-100 my-4 backdrop-blur-sm animate-float">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-ping"></span>
                        <span>Create New Password</span>
                    </div>

                    <p class="text-emerald-100/90 text-xs lg:text-sm leading-relaxed max-w-sm mx-auto">
                        Buat kata sandi baru yang kuat untuk menjaga keamanan data rekam medis & 
                        <strong class="text-white font-bold">Digital Twin</strong> Anda.
                    </p>
                </div>

                <div class="relative z-10 grid grid-cols-3 gap-3 border-t border-emerald-500/30 pt-6 text-center">
                    <div class="bg-white/10 p-2.5 rounded-2xl backdrop-blur-sm">
                        <span class="block text-[10px] sm:text-xs text-emerald-200 font-medium">Akses Aman</span>
                        <span class="text-xs sm:text-sm font-extrabold text-white">Terenkripsi</span>
                    </div>
                    <div class="bg-white/10 p-2.5 rounded-2xl backdrop-blur-sm">
                        <span class="block text-[10px] sm:text-xs text-emerald-200 font-medium">Password</span>
                        <span class="text-xs sm:text-sm font-extrabold text-white">Bcrypt Hash</span>
                    </div>
                    <div class="bg-white/10 p-2.5 rounded-2xl backdrop-blur-sm">
                        <span class="block text-[10px] sm:text-xs text-emerald-200 font-medium">Sesi Login</span>
                        <span class="text-xs sm:text-sm font-extrabold text-white">Diperbarui</span>
                    </div>
                </div>

            </div>

            <!-- ================= RIGHT PANEL ================= -->
            <div class="col-lg-6 right-panel bg-white p-8 lg:p-12 flex flex-col justify-center">

                <div class="auth-card max-w-md mx-auto w-full">

                    <?php if ($valid_token) : ?>

                        <div class="text-center lg:text-left mb-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl mb-4 font-bold text-xl">
                                🔐
                            </div>
                            <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">
                                Password Baru
                            </h2>
                            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                                Silakan masukkan password baru untuk akun Anda.
                            </p>
                        </div>

                        <?php if(isset($_GET['error_match'])) : ?>
                            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                                <span class="text-base">⚠️</span>
                                <span>Konfirmasi password tidak cocok. Silakan coba lagi.</span>
                            </div>
                        <?php endif; ?>

                        <form action="reset_process.php" method="POST" class="space-y-4">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                                    Password Baru
                                </label>
                                <div class="relative flex items-center">
                                    <input
                                        id="new_password"
                                        type="password"
                                        name="new_password"
                                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400 pr-12"
                                        placeholder="Minimal 6 karakter"
                                        required>
                                    <button
                                        type="button"
                                        class="absolute right-3 p-2 text-slate-400 hover:text-slate-600 transition text-sm font-bold"
                                        onclick="togglePassword('new_password')">
                                        👁
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                                    Konfirmasi Password Baru
                                </label>
                                <div class="relative flex items-center">
                                    <input
                                        id="confirm_password"
                                        type="password"
                                        name="confirm_password"
                                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400 pr-12"
                                        placeholder="Ketik ulang password baru"
                                        required>
                                    <button
                                        type="button"
                                        class="absolute right-3 p-2 text-slate-400 hover:text-slate-600 transition text-sm font-bold"
                                        onclick="togglePassword('confirm_password')">
                                        👁
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold py-3.5 rounded-2xl text-xs sm:text-sm transition-all duration-200 shadow-lg shadow-emerald-600/25 mt-2">
                                Simpan Password Baru
                            </button>
                        </form>

                    <?php else : ?>

                        <!-- Tampilan Jika Token Tidak Valid / Expired -->
                        <div class="text-center py-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-rose-100 text-rose-600 rounded-3xl mb-4 text-3xl">
                                ❌
                            </div>
                            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                                Tautan Tidak Valid / Kadaluwarsa
                            </h2>
                            <p class="text-slate-500 text-xs sm:text-sm mt-2 leading-relaxed">
                                Tautan untuk mereset password ini sudah tidak berlaku atau salah. Silakan ajukan ulang permintaan reset password.
                            </p>
                            <a href="forgot_password.php" 
                               class="inline-block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl text-xs sm:text-sm transition-all shadow-lg shadow-emerald-600/25 mt-6">
                                Minta Tautan Baru
                            </a>
                        </div>

                    <?php endif; ?>

                    <div class="text-center text-xs text-slate-500 mt-8 pt-6 border-t border-slate-100">
                        Kembali ke 
                        <a href="login.php" class="text-emerald-600 hover:text-emerald-700 font-extrabold hover:underline ml-1">
                            Halaman Login
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/auth.js"></script>

</body>

</html>