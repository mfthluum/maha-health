<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sertakan Koneksi Database
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
} elseif (file_exists('../../config/database.php')) {
    require_once '../../config/database.php';
} elseif (file_exists('../koneksi.php')) {
    require_once '../koneksi.php';
}

// 1. PROSES UPDATE PASSWORD BARU (Kalo Form Password Baru Di-submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $user_id          = intval($_POST['user_id']);
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || $new_password !== $confirm_password) {
        die("Password konfirmasi tidak cocok! <a href='forgot_password.php'>Kembali</a>");
    }

    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update_stmt->bind_param("si", $hashed_password, $user_id);

    if ($update_stmt->execute()) {
        header("Location: login.php?reset_success=1");
        exit();
    } else {
        die("Gagal memperbarui password!");
    }
}

// 2. CEK EMAIL TERDAFTAR ATAN TIDAK
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

$stmt = $conn->prepare("SELECT id, email, fullname FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: forgot_password.php?error=not_found");
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru | MAHA Health</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0f172a; }
        .glass-card { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(20px); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full glass-card p-8 rounded-3xl shadow-2xl">
        
        <!-- Notifikasi Email Cocok -->
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-start space-x-2">
            <span class="text-base">🎉</span>
            <div>
                <p class="font-bold">Email Terdaftar!</p>
                <p class="text-[11px] text-emerald-700 mt-0.5">Halo <b><?= htmlspecialchars($user['fullname'] ?? $user['email']) ?></b>, silakan buat password baru Anda di bawah ini.</p>
            </div>
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Password Baru</h2>
        <p class="text-slate-500 text-xs mb-6">Masukkan kata sandi baru untuk akun Anda.</p>

        <form action="forgot_process.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Password Baru</label>
                <input type="password" name="new_password" class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800" placeholder="Minimal 6 karakter" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-xs sm:text-sm outline-none text-slate-800" placeholder="Ketik ulang password baru" required>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold py-3.5 rounded-2xl text-xs sm:text-sm transition-all shadow-lg shadow-emerald-600/25 mt-4">
                Simpan & Update Password
            </button>
        </form>

    </div>

</body>
</html>