<?php
header('Content-Type: application/json');

// Tangkap input pesan dari chatbot.php
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['status' => 'error', 'reply' => 'Pesan tidak boleh kosong.']);
    exit;
}

// 🔑 Ambil API Key Groq dari Environment Variable Railway
$apiKey = getenv('GROQ_API_KEY');

if (empty($apiKey) && isset($_ENV['GROQ_API_KEY'])) {
    $apiKey = $_ENV['GROQ_API_KEY'];
}

if (empty($apiKey) && isset($_SERVER['GROQ_API_KEY'])) {
    $apiKey = $_SERVER['GROQ_API_KEY'];
}

if (empty($apiKey)) {
    echo json_encode([
        'status' => 'error',
        'reply' => 'Konfigurasi API Key Groq belum tersedia di server.'
    ]);
    exit;
}

// Endpoint resmi Groq Cloud
$url = "https://api.groq.com/openai/v1/chat/completions";

// System Instruction dengan easter egg pengembang MAHA Health
$systemInstruction = "Anda adalah AI ChatBot Medis resmi dari MAHA Health. Tugas Anda adalah memberikan edukasi kesehatan, konsultasi awal gejala medis, serta saran pola hidup sehat secara ramah, empati, dan ringkas. Selalu ingatkan pengguna untuk tetap berkonsultasi dengan dokter jika mengalami gejala berat atau darurat.\n\n"
    . "PENTING: Jika ada pengguna yang bertanya mengenai siapa yang membuat, mengembangkan, atau menciptakan aplikasi MAHA Health ini, Anda WAJIB menjawab dengan tegas dan penuh kebanggaan bahwa aplikasi MAHA Health ini dibuat oleh seorang developer muda yang tampan berusia 19 tahun bernama Muhamad Miftahul Ulum.";

$payload = [
   "model" => "openai/gpt-oss-120b",
    "messages" => [
        [
            "role" => "system",
            "content" => $systemInstruction
        ],
        [
            "role" => "user",
            "content" => $userMessage
        ]
    ],
    "temperature" => 0.7
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response, true);

if ($httpCode === 200 && isset($responseData['choices'][0]['message']['content'])) {
    $aiReply = $responseData['choices'][0]['message']['content'];
    echo json_encode(['status' => 'success', 'reply' => $aiReply]);
} else {
    $errorMessage = $responseData['error']['message'] ?? 'Gagal terhubung ke server Groq AI.';
    echo json_encode(['status' => 'error', 'reply' => $errorMessage]);
}
