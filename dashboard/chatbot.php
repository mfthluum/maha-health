<?php
// Include session check
require_once '../config/session.php';
require_once '../config/database.php';

// Ambil data user
$user_id   = $_SESSION['user']['id'] ?? null;
$user_name = $_SESSION['user']['fullname'] ?? 'Pengguna';

// Ambil daftar kontak SOS user yang sedang login
$sos_contacts_list = [];
if (isset($user_id)) {
    $q_sos = $conn->query("SELECT * FROM sos_contacts WHERE user_id = '$user_id' ORDER BY id DESC");
    if ($q_sos) {
        while ($row = $q_sos->fetch_assoc()) {
            $sos_contacts_list[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asisten AI Medis - MAHA Health</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Custom scrollbar biar clean */
        #chatBox::-webkit-scrollbar { width: 6px; }
        #chatBox::-webkit-scrollbar-track { background: transparent; }
        #chatBox::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        #chatBox::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- ==================== SIDEBAR NAVIGATION ==================== -->
    <aside class="w-64 bg-emerald-900 text-white flex flex-col justify-between shrink-0 border-r border-emerald-800 z-40">
        <div>
            <!-- Sidebar Header & Logo -->
            <div class="h-16 flex items-center space-x-3 px-6 bg-emerald-950/50 border-b border-emerald-800/60">
                <img src="../assets/img/logo/logo.png" alt="MAHA Health Logo" class="h-8 w-auto object-contain">
                <span class="text-lg font-extrabold tracking-tight text-white">MAHA<span class="text-emerald-400">HEALTH</span></span>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <a href="index.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/60 transition-all font-medium">
                    <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="chatbot.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-emerald-600 text-white font-semibold transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>AI ChatBot Medis</span>
                </a>
                <button onclick="openSosModal()" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/60 transition-all font-medium text-left">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Kontak SOS</span>
                    </div>
                    <span class="text-xs bg-red-500/20 text-red-300 px-2 py-0.5 rounded-full border border-red-500/30 font-bold">+ Hubungi</span>
                </button>
            </nav>
        </div>

        <!-- Sidebar Footer / User Info -->
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

    <!-- ==================== MAIN CONTENT CONTAINER ==================== -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

        <!-- Header Top Bar -->
        <header class="bg-white border-b border-slate-200 h-16 px-6 lg:px-8 flex justify-between items-center sticky top-0 z-30 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shadow-sm border border-emerald-200">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 leading-tight">Asisten AI Medis</h1>
                    <p class="text-xs text-slate-500">Konsultasi Kesehatan 24/7 • Didukung Groq Llama 3</p>
                </div>
            </div>

            <!-- Tombol Hapus Riwayat Chat -->
            <button onclick="clearChatHistory()" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-semibold px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-trash-can"></i>
                <span>Hapus Riwayat Chat</span>
            </button>
        </header>

        <!-- Chat Container Utama -->
        <main class="flex-1 p-4 md:p-6 flex flex-col overflow-hidden" style="height: calc(100vh - 64px);">
            <!-- Box Chat History (Scrollable, Putih) -->
            <div id="chatBox" class="flex-1 bg-white border border-slate-100 rounded-3xl p-6 md:p-8 overflow-y-auto space-y-5 mb-5 shadow-sm">
                <!-- Welcome Message akan di-render via JS -->
            </div>

            <!-- Input Bar -->
            <form id="chatForm" class="flex gap-3 bg-white p-3 border border-slate-100 rounded-full shadow-lg sticky bottom-0 z-40">
                <input type="text" id="userInput" placeholder="Ketik keluhan kamu di sini (misal: pusing dan mual)..."
                       class="flex-1 bg-transparent px-5 py-3 text-sm focus:outline-none text-slate-900 placeholder-slate-400" required autocomplete="off">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-7 py-3.5 rounded-full transition flex items-center gap-2.5 text-sm shadow-sm active:scale-95">
                    <span>Kirim Pesan</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </main>
    </div>

    <!-- ==================== MODAL POPUP: KELOLA & LIHAT NOMOR SOS ==================== -->
    <div id="sosModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white max-w-lg w-full rounded-2xl p-6 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
           
            <!-- Header Modal -->
            <div class="flex justify-between items-center pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Kelola Kontak Darurat SOS</h3>
                    <p class="text-xs text-slate-500">Tambah, lihat, edit, atau hapus nomor penerima sinyal SOS</p>
                </div>
                <button onclick="closeSosModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl">✕</button>
            </div>
            <div class="overflow-y-auto space-y-6 pt-4 pr-1">
               
                <!-- FORM TAMBAH / EDIT KONTAK -->
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
                    <button type="submit" id="btnSubmitSos" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-xs transition-all shadow-sm">
                        Simpan Kontak SOS
                    </button>
                </form>

                <!-- DAFTAR KONTAK TERSIMPAN -->
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kontak Tersimpan (<?= count($sos_contacts_list); ?>)</h4>
                   
                    <?php if (empty($sos_contacts_list)): ?>
                        <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-400">Belum ada nomor darurat tersimpan.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            <?php foreach ($sos_contacts_list as $contact): ?>
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-emerald-300 transition-all">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs uppercase
                                            <?= $contact['contact_type'] == 'keluarga' ? 'bg-blue-100 text-blue-700' : ($contact['contact_type'] == 'rumah_sakit' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'); ?>">
                                            <?= substr($contact['contact_type'], 0, 1); ?>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($contact['name']); ?></p>
                                            <p class="text-[11px] text-slate-500"><?= htmlspecialchars($contact['phone_number']); ?> • <span class="capitalize font-semibold"><?= str_replace('_', ' ', $contact['contact_type']); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <button type="button" onclick="editSosContact(<?= htmlspecialchars(json_encode($contact)); ?>)" class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <a href="delete_sos_contact.php?id=<?= $contact['id']; ?>" onclick="return confirm('Yakin ingin menghapus kontak ini?')" class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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

    <!-- ==================== SCRIPT ==================== -->
    <script>
        // ==================== PERSISTENT CHAT HISTORY ====================
        const USER_ID = <?= json_encode($user_id ?? 0); ?>;
        const CHAT_KEY = 'maha_chat_history_' + USER_ID;

        // Simpan seluruh riwayat ke localStorage
        function saveChatHistory() {
            const messages = [];
            document.querySelectorAll('#chatBox > div').forEach(div => {
                const isUser = div.classList.contains('justify-end');
                const textEl = div.querySelector('div.p-5');
                if (textEl) {
                    messages.push({
                        sender: isUser ? 'user' : 'ai',
                        text: textEl.innerHTML
                    });
                }
            });
            localStorage.setItem(CHAT_KEY, JSON.stringify(messages));
        }

        // Muat riwayat dari localStorage
        function loadChatHistory() {
            const saved = localStorage.getItem(CHAT_KEY);
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = '';

            if (saved) {
                const messages = JSON.parse(saved);
                if (messages.length > 0) {
                    messages.forEach(msg => {
                        appendMessage(msg.sender, msg.text, false); // false = jangan simpan lagi
                    });
                    return;
                }
            }
            // Jika kosong → tampilkan welcome message
            showWelcomeMessage();
        }

        // Welcome message default
        function showWelcomeMessage() {
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = `
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0 shadow-sm border border-emerald-200">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="bg-slate-100 border border-slate-200 p-5 rounded-3xl rounded-tl-none max-w-[85%] text-sm text-slate-800 leading-relaxed shadow-sm">
                        Selamat datang di <strong>MAHA Health</strong>, 🩺<br><br>
                        Saya adalah asisten virtual medis kamu. Kamu bisa menanyakan apa saja, mulai dari keluhan gejala, tips nutrisi, hingga pola hidup sehat.<br><br>
                        <strong>Contoh:</strong> <em>"Saya batuk kering sejak 3 hari lalu dan agak demam, apa yang harus saya lakukan?"</em>
                    </div>
                </div>
            `;
        }

        // Hapus seluruh riwayat chat
        function clearChatHistory() {
            Swal.fire({
                title: 'Hapus Seluruh Riwayat?',
                text: 'Semua percakapan dengan AI akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(CHAT_KEY);
                    showWelcomeMessage();
                    Swal.fire({
                        icon: 'success',
                        title: 'Riwayat Dihapus',
                        text: 'Percakapan telah dibersihkan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        // ==================== CHAT LOGIC ====================
        const chatForm = document.getElementById('chatForm');
        const userInput = document.getElementById('userInput');
        const chatBox = document.getElementById('chatBox');

        // Load history saat halaman dibuka
        document.addEventListener('DOMContentLoaded', loadChatHistory);

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = userInput.value.trim();
            if (!message) return;

            // Render pesan user
            appendMessage('user', message);
            userInput.value = '';

            // Loading AI
            const loadingId = appendLoading();

            try {
                const response = await fetch('api_chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });
                const data = await response.json();
                removeLoading(loadingId);

                if (data.status === 'success') {
                    appendMessage('ai', data.reply);
                } else {
                    appendMessage('ai', '⚠️ **Error:** ' + data.reply);
                }
            } catch (err) {
                removeLoading(loadingId);
                appendMessage('ai', '⚠️ **Maaf, gagal terhubung ke server backend.** Coba lagi nanti co!');
            }
        });

        function appendMessage(sender, text, shouldSave = true) {
            const isUser = sender === 'user';
            const msgDiv = document.createElement('div');
            msgDiv.className = isUser ? 'flex gap-3 justify-end' : 'flex gap-4';

            // Jika text sudah berupa HTML (dari history), jangan proses lagi
            let formattedText = text;
            if (!text.includes('<br>') && !text.includes('<strong>')) {
                formattedText = text.replace(/\n/g, '<br>');
                formattedText = formattedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            }

            msgDiv.innerHTML = `
                ${!isUser ? '<div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0 shadow-sm border border-emerald-200"><i class="fa-solid fa-robot"></i></div>' : ''}
                <div class="${isUser ? 'bg-emerald-500 text-white rounded-3xl rounded-tr-none' : 'bg-slate-100 border border-slate-200 text-slate-800 rounded-3xl rounded-tl-none'} p-5 rounded-3xl max-w-[85%] text-sm leading-relaxed shadow-sm">
                    ${formattedText}
                </div>
                ${isUser ? '<div class="w-8 h-8 rounded-full bg-slate-700 text-slate-100 font-bold flex items-center justify-center text-xs flex-shrink-0 shadow-sm border border-slate-600"><i class="fa-solid fa-user"></i></div>' : ''}
            `;
            chatBox.appendChild(msgDiv);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Simpan ke localStorage setiap ada pesan baru
            if (shouldSave) {
                saveChatHistory();
            }
        }

        function appendLoading() {
            const id = 'loading-' + Date.now();
            const loadingDiv = document.createElement('div');
            loadingDiv.id = id;
            loadingDiv.className = 'flex gap-4';
            loadingDiv.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0 shadow-sm border border-emerald-200"><i class="fa-solid fa-robot"></i></div>
                <div class="bg-slate-100 border border-slate-200 p-5 rounded-3xl rounded-tl-none text-sm text-slate-500 italic flex items-center gap-3">
                    <i class="fa-solid fa-spinner animate-spin text-emerald-500"></i> AI sedang menganalisis gejala kamu...
                </div>
            `;
            chatBox.appendChild(loadingDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
            return id;
        }

        function removeLoading(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        // ==================== MODAL SOS ====================
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
    </script>
</body>
</html>