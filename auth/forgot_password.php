<?php
session_start();

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
    <title>Lupa Password | MAHA Health</title>

    <link rel="icon" href="../assets/img/logo/logo.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
        }
        .auth-bg-glow-1 {
            position: absolute; top: -10%; left: -10%; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.25) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%; pointer-events: none;
        }
        .auth-bg-glow-2 {
            position: absolute; bottom: -10%; right: -10%; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(5,150,105,0.2) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%; pointer-events: none;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body class="min-h-screen relative overflow-x-hidden flex items-center justify-center p-4 lg:p-8">

    <div class="auth-bg-glow-1"></div>
    <div class="auth-bg-glow-2"></div>

    <div class="container-fluid max-w-6xl relative z-10">
        <div class="row auth-container overflow-hidden rounded-3xl glass-card border-0">

            <!-- LEFT PANEL -->
            <div class="col-lg-6 left-panel bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                <div class="my-auto py-6 relative z-10 text-center flex flex-col items-center">
                    <div class="w-36 h-36 lg:w-48 lg:h-48 bg-white/15 backdrop-blur-lg rounded-3xl p-6 border-2 border-white/30 shadow-2xl flex items-center justify-center mb-8">
                        <img src="../assets/img/logo/logo.png" alt="Logo" class="w-full h-full object-contain filter drop-shadow-2xl" onerror="this.src='https://via.placeholder.com/200?text=MAHA';">
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-black tracking-wider text-white">MAHA<span class="text-emerald-300">HEALTH</span></h1>
                    <p class="text-emerald-100/90 text-xs lg:text-sm mt-4 max-w-sm">Pemulihan kata sandi akun Digital Twin Anda.</p>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="col-lg-6 right-panel bg-white p-8 lg:p-12 flex flex-col justify-center">
                <div class="auth-card max-w-md mx-auto w-full">

                    <div class="text-center lg:text-left mb-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl mb-4 font-bold text-xl">🔑</div>
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">Lupa Password?</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-1">Masukkan email terdaftar Anda untuk verifikasi.</p>
                    </div>

                    <?php if (isset($_GET['error']) && $_GET['error'] === 'not_found') : ?>
                        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                            <span class="text-base">⚠️</span>
                            <span>Email tidak terdaftar di sistem kami!</span>
                        </div>
                    <?php endif; ?>

                    <form action="forgot_process.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Email Terdaftar</label>
                            <input type="email" name="email" class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800 placeholder-slate-400" placeholder="nama@email.com" required>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold py-3.5 rounded-2xl text-xs sm:text-sm transition-all duration-200 shadow-lg shadow-emerald-600/25 mt-2">
                            Cek Email & Lanjut
                        </button>
                    </form>

                    <div class="text-center text-xs text-slate-500 mt-8 pt-6 border-t border-slate-100">
                        Kembali ke <a href="login.php" class="text-emerald-600 hover:text-emerald-700 font-extrabold hover:underline ml-1">Halaman Login</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</body>
</html>