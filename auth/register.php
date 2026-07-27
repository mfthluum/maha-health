<?php
session_start();

// Jika sudah login
if (isset($_SESSION['user'])) {
    header("Location: ../dashboard/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MAHA Health</title>

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

        /* Floating Badge Animation */
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

    <!-- Ambient Glow Background Effects -->
    <div class="auth-bg-glow-1"></div>
    <div class="auth-bg-glow-2"></div>

    <div class="container-fluid max-w-6xl relative z-10">

        <div class="row auth-container overflow-hidden rounded-3xl glass-card border-0">

            <!-- ================= LEFT PANEL (VISUAL BRANDING & ICONIC LOGO) ================= -->
            <div class="col-lg-6 left-panel bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                
                <!-- Background Pattern overlay -->
                <div class="absolute inset-0 bg-black/10 pointer-events-none"></div>

                <!-- Center Hero Section dengan Logo Iconic Super Besar -->
                <div class="my-auto py-6 relative z-10 text-center flex flex-col items-center">
                    
                    <!-- CONTAINER LOGO ICONIC SUPER BESAR -->
                    <div class="relative mb-8 flex items-center justify-center">
                        <!-- Pendaran cahaya (glow) belakang logo -->
                        <div class="absolute w-48 h-48 lg:w-56 lg:h-56 bg-emerald-300/40 rounded-full blur-3xl animate-pulse"></div>
                        
                        <!-- Box Kaca Logo Utama -->
                        <div class="w-36 h-36 lg:w-48 lg:h-48 bg-white/15 backdrop-blur-lg rounded-3xl p-6 border-2 border-white/30 shadow-2xl flex items-center justify-center transform transition duration-500 hover:scale-105 hover:rotate-1">
                            <img src="../assets/img/logo/logo.png" 
                                 alt="MAHA Health Logo" 
                                 class="w-full h-full object-contain filter drop-shadow-2xl"
                                 onerror="this.src='https://via.placeholder.com/200?text=MAHA';">
                        </div>
                    </div>

                    <!-- Header Brand Title -->
                    <h1 class="text-3xl lg:text-4xl font-black tracking-wider text-white">
                        MAHA<span class="text-emerald-300">HEALTH</span>
                    </h1>

                    <!-- Badge Platform -->
                    <div class="inline-flex items-center space-x-2 bg-emerald-500/30 border border-emerald-300/30 px-4 py-1.5 rounded-full text-xs font-bold text-emerald-100 my-4 backdrop-blur-sm animate-float">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-ping"></span>
                        <span>Your Digital Health Twin</span>
                    </div>

                    <p class="text-emerald-100/90 text-xs lg:text-sm leading-relaxed max-w-sm mx-auto">
                        Buat akun dan mulai perjalanan hidup sehatmu bersama <strong class="text-white font-bold">MAHA Health</strong>.
                    </p>
                </div>

                <!-- Bottom Stats Badges -->
                <div class="relative z-10 grid grid-cols-3 gap-3 border-t border-emerald-500/30 pt-6 text-center">
                    <div class="bg-white/10 p-2.5 rounded-2xl backdrop-blur-sm">
                        <span class="block text-[10px] sm:text-xs text-emerald-200 font-medium">Digital Twin</span>
                        <span class="text-xs sm:text-sm font-extrabold text-white">Interactive</span>
                    </div>
                    <div class="bg-white/10 p-2.5 rounded-2xl backdrop-blur-sm">
                        <span class="block text-[10px] sm:text-xs text-emerald-200 font-medium">Health Score</span>
                        <span class="text-xs sm:text-sm font-extrabold text-white">Dynamic</span>
                    </div>
                    <div class="bg-white/10 p-2.5 rounded-2xl backdrop-blur-sm">
                        <span class="block text-[10px] sm:text-xs text-emerald-200 font-medium">Rekomendasi</span>
                        <span class="text-xs sm:text-sm font-extrabold text-white">Medis DB</span>
                    </div>
                </div>

            </div>

            <!-- ================= RIGHT PANEL (FORM REGISTER) ================= -->
            <div class="col-lg-6 right-panel bg-white p-8 lg:p-12 flex flex-col justify-center">

                <div class="auth-card max-w-md mx-auto w-full">

                    <!-- Title Header -->
                    <div class="text-center lg:text-left mb-6">
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Create Account
                        </h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-1">
                            Daftar untuk memulai perjalanan sehatmu
                        </p>
                    </div>

                    <!-- Alert Error -->
                    <?php if(isset($_GET['error'])) : ?>
                        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                            <span class="text-base">⚠️</span>
                            <span><?= htmlspecialchars($_GET['error']) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Form Register -->
                    <form action="register_process.php" method="POST" onsubmit="return validateRegister()" class="space-y-3.5">

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="fullname"
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400"
                                placeholder="Masukkan Nama Lengkap"
                                autocomplete="name"
                                required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Email
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400"
                                placeholder="Masukkan Email"
                                autocomplete="email"
                                required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Password
                            </label>
                            <div class="relative flex items-center">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400 pr-12"
                                    placeholder="Minimal 8 karakter"
                                    autocomplete="new-password"
                                    required>
                                <button
                                    type="button"
                                    class="absolute right-3 p-1.5 text-slate-400 hover:text-slate-600 transition text-sm font-bold"
                                    onclick="togglePassword('password')">
                                    👁
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Konfirmasi Password
                            </label>
                            <div class="relative flex items-center">
                                <input
                                    id="confirm_password"
                                    type="password"
                                    name="confirm_password"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400 pr-12"
                                    placeholder="Ulangi Password"
                                    autocomplete="new-password"
                                    required>
                                <button
                                    type="button"
                                    class="absolute right-3 p-1.5 text-slate-400 hover:text-slate-600 transition text-sm font-bold"
                                    onclick="togglePassword('confirm_password')">
                                    👁
                                </button>
                            </div>
                        </div>

                        <!-- Tinggi & Berat Badan -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Tinggi (cm)
                                </label>
                                <input
                                    type="number"
                                    name="height"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400"
                                    placeholder="170"
                                    min="50"
                                    max="250">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Berat (kg)
                                </label>
                                <input
                                    type="number"
                                    name="weight"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400"
                                    placeholder="60"
                                    min="10"
                                    max="300">
                            </div>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Jenis Kelamin
                            </label>
                            <select
                                name="gender"
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 bg-white"
                                required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Male">Laki-laki</option>
                                <option value="Female">Perempuan</option>
                            </select>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Tanggal Lahir
                            </label>
                            <input
                                type="date"
                                name="birth_date"
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 bg-white"
                                required>
                        </div>

                        <!-- Tombol Submit -->
                        <button
                            type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold py-3.5 rounded-2xl text-xs sm:text-sm transition-all duration-200 shadow-lg shadow-emerald-600/25 mt-4">
                            Create Account
                        </button>

                    </form>

                    <!-- Link Login -->
                    <div class="register-link text-center text-xs text-slate-500 mt-6 pt-5 border-t border-slate-100">
                        Sudah punya akun?
                        <a href="login.php" class="text-emerald-600 hover:text-emerald-700 font-extrabold hover:underline ml-1">
                            Login
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Script JS Utama (Tidak diubah) -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/auth.js"></script>

</body>

</html>