<?php
session_start();

// Matikan display error HTML agar output JSON bersih
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Koneksi Database Fleksibel
    if (file_exists('../config/database.php')) {
        require_once '../config/database.php';
    } elseif (file_exists('../../config/database.php')) {
        require_once '../../config/database.php';
    } elseif (file_exists('../koneksi.php')) {
        require_once '../koneksi.php';
    } else {
        throw new Exception("File koneksi database tidak ditemukan!");
    }

    // 2. Ambil Session User
    $user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['id_user'] ?? $_SESSION['user']['id'] ?? null;

    if (!$user_id) {
        echo json_encode(['status' => false, 'message' => 'Session login tidak ditemukan. Silakan login kembali.']);
        exit;
    }

    // 3. Ambil Koordinat GPS
    $lat = $_POST['latitude'] ?? 'Tidak diketahui';
    $lng = $_POST['longitude'] ?? 'Tidak diketahui';

    if ($lat !== 'Tidak diketahui' && $lng !== 'Tidak diketahui') {
        $maps_url = "https://maps.google.com/?q={$lat},{$lng}";
        $coords_info = "{$lat}, {$lng}";
    } else {
        $maps_url = "Akses lokasi GPS tidak diaktifkan";
        $coords_info = "Tidak diketahui";
    }

    // 4. Ambil Data User Profile dari Tabel `users`
    $fullname = 'Pasien';
    $birth_date = '-';
    $age = 'Tidak diketahui';
    $user_height = '-';
    $user_weight = '-';

    $user_query = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
    if ($user_query && $u_data = $user_query->fetch_assoc()) {
        $fullname = !empty($u_data['fullname']) ? $u_data['fullname'] : ($u_data['name'] ?? 'Pasien');
        
        if (!empty($u_data['height'])) {
            $user_height = floatval($u_data['height']);
        }
        if (!empty($u_data['weight'])) {
            $user_weight = floatval($u_data['weight']);
        }

        $tgl_lahir = $u_data['birth_date'] ?? null;
        if ($tgl_lahir && $tgl_lahir != '0000-00-00') {
            $birth_date = date('d-m-Y', strtotime($tgl_lahir));
            $dob = new DateTime($tgl_lahir);
            $now = new DateTime();
            $age = $now->diff($dob)->y . ' Tahun';
        }
    }

    // 5. Ambil Rekam Medis Kesehatan Terakhir (health_records)
    $hr_query = $conn->query("SELECT * FROM health_records WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
    $med = ($hr_query && $hr_query->num_rows > 0) ? $hr_query->fetch_assoc() : [];

    // Ambil kolom kesehatan vital
    $heart_rate = $med['heart_rate'] ?? $med['detak_jantung'] ?? '-';
    $systolic   = $med['systolic'] ?? $med['sistolik'] ?? '-';
    $diastolic  = $med['diastolic'] ?? $med['diastolik'] ?? '-';
    $spo2       = $med['spo2'] ?? $med['kadar_oksigen'] ?? '-';
    $temp       = $med['temperature'] ?? $med['suhu'] ?? '-';
    $hydration  = $med['hydration'] ?? $med['hidrasi'] ?? '-';
    $sleep      = $med['sleep_hours'] ?? $med['durasi_tidur'] ?? '-';
    $steps      = $med['steps'] ?? $med['langkah_kaki'] ?? '-';

    $height = !empty($med['height']) ? floatval($med['height']) : $user_height;
    $weight = !empty($med['weight']) ? floatval($med['weight']) : $user_weight;

    $bp = ($systolic !== '-' && $diastolic !== '-') ? "{$systolic}/{$diastolic} mmHg" : "-";

    // -------------------------------------------------------------
    // 🧠 PENYUSUNAN RINGKASAN & ANALISIS MEDIS
    // -------------------------------------------------------------
    $warnings = [];

    // Detak Jantung
    if (is_numeric($heart_rate)) {
        if ($heart_rate < 60 || $heart_rate > 100) {
            $warnings[] = "Detak Jantung Abnormal ({$heart_rate} BPM)";
        }
    }

    // Tekanan Darah
    if (is_numeric($systolic) && is_numeric($diastolic)) {
        if ($systolic > 140 || $diastolic > 90) {
            $warnings[] = "Tekanan Darah Tinggi/Hipertensi ({$bp})";
        } elseif ($systolic < 90 || $diastolic < 60) {
            $warnings[] = "Tekanan Darah Rendah/Hipotensi ({$bp})";
        }
    }

    // Kadar Oksigen (SpO2)
    if (is_numeric($spo2)) {
        if ($spo2 < 95) {
            $warnings[] = "Kadar Oksigen Rendah/Hipoksia ({$spo2}%)";
        }
    }

    // Suhu Tubuh
    if (is_numeric($temp)) {
        if ($temp > 37.5) {
            $warnings[] = "Suhu Tubuh Tinggi/Demam ({$temp} °C)";
        } elseif ($temp < 35.5) {
            $warnings[] = "Suhu Tubuh Dingin/Hipotermia ({$temp} °C)";
        }
    }

    // Hidrasi & Tidur
    if (is_numeric($hydration) && $hydration < 50) {
        $warnings[] = "Tingkat Hidrasi Rendah ({$hydration}%)";
    }

    if (is_numeric($sleep) && $sleep < 5) {
        $warnings[] = "Kurang Istirahat/Tidur ({$sleep} Jam)";
    }

    // Format List Poin Ringkasan Medis
    $medical_summary = "";
    if (!empty($warnings)) {
        foreach ($warnings as $w) {
            $medical_summary .= "   ▫️ {$w}\n";
        }
    } else {
        $medical_summary = "   ▫️ Kondisi vital pasien terpantau stabil / normal.\n";
    }

    // 6. Ambil Kontak SOS User
    $contacts_query = $conn->query("SELECT phone_number FROM sos_contacts WHERE user_id = '$user_id'");
    $contacts = [];
    if ($contacts_query) {
        while ($row = $contacts_query->fetch_assoc()) {
            if (!empty($row['phone_number'])) {
                $contacts[] = $row['phone_number'];
            }
        }
    }

    if (empty($contacts)) {
        echo json_encode([
            'status' => false, 
            'message' => 'Belum ada nomor kontak SOS yang tersimpan. Silakan tambahkan nomor kontak terlebih dahulu.'
        ]);
        exit;
    }

    // 7. Susun Pesan WhatsApp SOS Rapi
    $message  = "🚨 *PANGGILAN DARURAT MAHA HEALTH (SOS)* 🚨\n";
    $message .= "========================================\n\n";
    
    $message .= "👤 *DATA PASIEN:*\n";
    $message .= "• Nama Lengkap : {$fullname}\n";
    $message .= "• Tanggal Lahir : {$birth_date}\n";
    $message .= "• Usia          : {$age}\n\n";

    $message .= "📍 *LOKASI TERAKHIR PASIEN:*\n";
    $message .= "• Koordinat     : {$coords_info}\n";
    $message .= "• Peta Google   : {$maps_url}\n\n";

    $message .= "🩺 *RINGKASAN & ANALISIS MEDIS:*\n";
    $message .= "{$medical_summary}\n";

    $message .= "📊 *METRIK VITAL TERAKHIR PASIEN:*\n";
    $message .= "• Detak Jantung  : {$heart_rate} BPM\n";
    $message .= "• Tekanan Darah  : {$bp}\n";
    $message .= "• Kadar Oksigen  : {$spo2} %\n";
    $message .= "• Suhu Tubuh     : {$temp} °C\n";
    $message .= "• Tingkat Hidrasi : {$hydration} %\n";
    $message .= "• Durasi Tidur   : {$sleep} Jam\n";
    $message .= "• Langkah Kaki   : {$steps} Langkah\n";
    $message .= "• Berat Badan    : {$weight} kg\n";
    $message .= "• Tinggi Badan   : {$height} cm\n\n";

    $message .= "⚠️ *MOHON SEGERA HUBUNGI PASIEN ATAU KIRIMKAN BANTUAN AMBULANS / RUMAH SAKIT TERDEKAT!*";

    // 8. Token Fonnte
    $fonnte_token = "K4Rzz2UHFAmXq8Gu7Pi7";
    $target_numbers = implode(',', $contacts);

    // 9. Kirim via cURL Fonnte
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POSTFIELDS => array(
            'target' => $target_numbers,
            'message' => $message,
            'countryCode' => '62',
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: {$fonnte_token}"
        ),
    ));

    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($curl_error) {
        echo json_encode(['status' => false, 'message' => 'Gagal koneksi Fonnte: ' . $curl_error]);
    } else {
        echo json_encode(['status' => true, 'message' => 'Sinyal SOS & WhatsApp berhasil terkirim ke kontak darurat!']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'System Error: ' . $e->getMessage()]);
}
exit;