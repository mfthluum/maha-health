-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 27 Jul 2026 pada 15.50
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maha_health`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `character_levels`
--

CREATE TABLE `character_levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(100) DEFAULT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `expression` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `min_score` int(11) DEFAULT NULL,
  `max_score` int(11) DEFAULT NULL,
  `health_color` varchar(20) DEFAULT NULL,
  `animation` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `character_levels`
--

INSERT INTO `character_levels` (`id`, `level_name`, `image_name`, `expression`, `status`, `min_score`, `max_score`, `health_color`, `animation`, `description`, `created_at`) VALUES
(1, 'Level 1', 'dt_lv1_healthy.png', 'Senyum Ceria', 'Healthy', 90, 100, '#22C55E', 'bounce', 'Tubuh dalam kondisi sangat sehat, seluruh parameter berada pada rentang normal.', '2026-07-25 20:40:29'),
(2, 'Level 2', 'dt_lv2_normal.png', 'Senyum Biasa', 'Normal', 80, 89, '#84CC16', 'idle', 'Kondisi tubuh cukup baik, namun ada beberapa parameter yang mulai menurun.', '2026-07-25 20:40:29'),
(3, 'Level 3', 'dt_lv3_tired.png', 'Wajah Lelah', 'Fatigue', 65, 79, '#FACC15', 'slow_breathing', 'Tubuh mulai mengalami kelelahan akibat aktivitas, kurang tidur, atau dehidrasi ringan.', '2026-07-25 20:40:29'),
(4, 'Level 4', 'dt_lv4_warning.png', 'Pucat Berkeringat', 'Warning', 40, 64, '#F97316', 'shaking', 'Beberapa parameter vital berada di luar batas normal dan memerlukan perhatian.', '2026-07-25 20:40:29'),
(5, 'Level 5', 'dt_lv5_critical.png', 'Terbaring Darurat', 'Critical', 0, 39, '#EF4444', 'alarm', 'Kondisi tubuh berada pada tingkat berbahaya dan membutuhkan pertolongan medis.', '2026-07-25 20:40:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `health_conditions`
--

CREATE TABLE `health_conditions` (
  `id` int(11) NOT NULL,
  `condition_name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `risk_level` enum('Low','Medium','High','Emergency') DEFAULT NULL,
  `recommendation_title` varchar(255) DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `character_level` int(11) DEFAULT NULL,
  `min_health_score` int(11) DEFAULT NULL,
  `max_health_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `health_conditions`
--

INSERT INTO `health_conditions` (`id`, `condition_name`, `category`, `description`, `risk_level`, `recommendation_title`, `recommendation`, `character_level`, `min_health_score`, `max_health_score`) VALUES
(1, 'Sehat', 'Healthy', 'Seluruh parameter kesehatan berada pada rentang normal.', 'Low', 'Pertahankan Pola Hidup', 'Teruskan pola makan sehat, olahraga rutin, tidur cukup, dan minum air yang cukup.', 1, 90, 100),
(2, 'Kondisi Normal', 'Normal', 'Terdapat sedikit penyimpangan tetapi masih dalam batas aman.', 'Low', 'Tingkatkan Gaya Hidup', 'Perbanyak aktivitas fisik dan tetap menjaga pola tidur.', 2, 80, 89),
(3, 'Kelelahan', 'Fatigue', 'Tubuh menunjukkan tanda-tanda kelelahan akibat aktivitas atau kurang istirahat.', 'Medium', 'Istirahat', 'Kurangi aktivitas berat dan tidur minimal 7 jam.', 3, 70, 79),
(4, 'Kurang Tidur', 'Sleep', 'Durasi tidur berada di bawah rekomendasi.', 'Medium', 'Perbaiki Pola Tidur', 'Usahakan tidur 7–9 jam setiap malam.', 3, 70, 79),
(5, 'Dehidrasi Ringan', 'Hydration', 'Asupan cairan tubuh mulai berkurang.', 'Medium', 'Perbanyak Minum', 'Minumlah minimal 2 liter air putih setiap hari.', 3, 65, 74),
(6, 'Hipertensi Tahap 1', 'Blood Pressure', 'Tekanan darah berada di atas batas normal.', 'High', 'Kontrol Tekanan Darah', 'Kurangi konsumsi garam dan lakukan pemeriksaan rutin.', 4, 55, 69),
(7, 'Hipotensi', 'Blood Pressure', 'Tekanan darah lebih rendah dari normal.', 'Medium', 'Tingkatkan Cairan', 'Perbanyak minum air dan hindari berdiri terlalu cepat.', 4, 55, 69),
(8, 'Demam', 'Temperature', 'Suhu tubuh meningkat di atas normal.', 'Medium', 'Istirahat', 'Perbanyak istirahat dan minum air.', 4, 55, 69),
(9, 'Hipoksia Ringan', 'Oxygen', 'Kadar oksigen mulai menurun.', 'High', 'Pantau Pernapasan', 'Istirahat dan segera periksa jika SpO₂ terus menurun.', 4, 45, 59),
(10, 'Obesitas', 'BMI', 'Indeks massa tubuh berada pada kategori obesitas.', 'High', 'Turunkan Berat Badan', 'Atur pola makan dan rutin berolahraga.', 4, 50, 69),
(11, 'Kekurangan Berat Badan', 'BMI', 'Indeks massa tubuh berada di bawah normal.', 'Medium', 'Perbaiki Nutrisi', 'Konsumsi makanan bergizi dengan kalori yang cukup.', 4, 60, 74),
(12, 'Stres Fisik', 'Fatigue', 'Tubuh menunjukkan tanda stres akibat aktivitas berlebihan.', 'Medium', 'Recovery', 'Kurangi aktivitas fisik selama beberapa hari.', 4, 50, 64),
(13, 'Risiko Penyakit Jantung', 'Cardiovascular', 'Detak jantung dan tekanan darah menunjukkan risiko gangguan jantung.', 'High', 'Periksa Dokter', 'Segera lakukan pemeriksaan ke dokter spesialis.', 5, 35, 49),
(14, 'Gangguan Pernapasan', 'Respiratory', 'Nilai SpO₂ sangat rendah.', 'Emergency', 'Cari Pertolongan Medis', 'Segera menuju fasilitas kesehatan terdekat.', 5, 20, 39),
(15, 'Kondisi Kritis', 'Critical', 'Beberapa parameter vital berada pada kondisi berbahaya.', 'Emergency', 'Segera Cari Bantuan', 'Tekan tombol SOS dan segera menuju rumah sakit.', 5, 0, 19);

-- --------------------------------------------------------

--
-- Struktur dari tabel `health_recommendations`
--

CREATE TABLE `health_recommendations` (
  `id` int(11) NOT NULL,
  `condition_name` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `food_recommendation` text DEFAULT NULL,
  `exercise_recommendation` text DEFAULT NULL,
  `water_recommendation` text DEFAULT NULL,
  `sleep_recommendation` text DEFAULT NULL,
  `priority` enum('Low','Medium','High','Emergency') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `health_recommendations`
--

INSERT INTO `health_recommendations` (`id`, `condition_name`, `title`, `recommendation`, `food_recommendation`, `exercise_recommendation`, `water_recommendation`, `sleep_recommendation`, `priority`, `created_at`) VALUES
(1, 'Sehat', 'Pertahankan Pola Hidup Sehat', 'Semua indikator kesehatan berada pada kondisi baik. Pertahankan gaya hidup sehat.', 'Perbanyak buah, sayur, protein, dan makanan seimbang.', 'Olahraga ringan hingga sedang 30 menit per hari.', 'Minum minimal 2 liter air setiap hari.', 'Tidur 7-9 jam setiap malam.', 'Low', '2026-07-25 20:38:31'),
(2, 'Kondisi Normal', 'Tingkatkan Kualitas Hidup', 'Terdapat sedikit penyimpangan pada beberapa parameter kesehatan.', 'Kurangi makanan cepat saji dan gula berlebih.', 'Jalan kaki atau jogging 30 menit.', '2-2.5 liter air per hari.', 'Tidur minimal 7 jam.', 'Low', '2026-07-25 20:38:31'),
(3, 'Kelelahan', 'Istirahatkan Tubuh', 'Tubuh membutuhkan waktu pemulihan.', 'Perbanyak makanan tinggi protein dan vitamin.', 'Hindari olahraga berat selama pemulihan.', '2.5 liter air per hari.', 'Tidur minimal 8 jam.', 'Medium', '2026-07-25 20:38:31'),
(4, 'Kurang Tidur', 'Perbaiki Pola Tidur', 'Kurang tidur dapat memengaruhi daya tahan tubuh dan konsentrasi.', 'Hindari kopi pada malam hari.', 'Stretching ringan.', '2 liter air.', 'Tidur 7-9 jam secara konsisten.', 'Medium', '2026-07-25 20:38:31'),
(5, 'Dehidrasi Ringan', 'Tingkatkan Asupan Cairan', 'Tubuh mulai mengalami kekurangan cairan.', 'Perbanyak buah yang mengandung air.', 'Kurangi aktivitas di bawah terik matahari.', '2.5-3 liter air.', 'Tidur cukup agar pemulihan optimal.', 'Medium', '2026-07-25 20:38:31'),
(6, 'Hipotensi', 'Naikkan Tekanan Darah Secara Alami', 'Tekanan darah berada di bawah batas normal.', 'Tambahkan makanan yang mengandung elektrolit sesuai kebutuhan.', 'Hindari berdiri terlalu cepat.', '2.5 liter air.', 'Tidur cukup.', 'Medium', '2026-07-25 20:38:31'),
(7, 'Hipertensi Tahap 1', 'Kontrol Tekanan Darah', 'Tekanan darah mulai meningkat.', 'Kurangi garam dan makanan berlemak.', 'Jalan santai 30 menit.', '2 liter air.', 'Tidur cukup.', 'High', '2026-07-25 20:38:31'),
(8, 'Demam', 'Istirahat Total', 'Suhu tubuh meningkat di atas normal.', 'Perbanyak makanan bergizi dan mudah dicerna.', 'Hindari olahraga.', '3 liter air.', 'Tidur sebanyak mungkin.', 'Medium', '2026-07-25 20:38:31'),
(9, 'Hipoksia Ringan', 'Pantau Pernapasan', 'Kadar oksigen mulai menurun.', 'Konsumsi makanan bergizi.', 'Hindari aktivitas berat.', '2 liter air.', 'Istirahat cukup.', 'High', '2026-07-25 20:38:31'),
(10, 'Obesitas', 'Turunkan Berat Badan', 'BMI menunjukkan obesitas.', 'Kurangi gula dan makanan berlemak.', 'Cardio 150 menit per minggu.', '2-3 liter air.', 'Tidur minimal 7 jam.', 'High', '2026-07-25 20:38:31'),
(11, 'Kekurangan Berat Badan', 'Tingkatkan Berat Badan Sehat', 'BMI berada di bawah normal.', 'Perbanyak protein dan kalori sehat.', 'Latihan beban ringan.', '2 liter air.', 'Tidur cukup.', 'Medium', '2026-07-25 20:38:31'),
(12, 'Stres Fisik', 'Recovery', 'Tubuh mengalami stres akibat aktivitas berlebihan.', 'Perbanyak makanan bergizi.', 'Istirahat aktif.', '2.5 liter air.', 'Tidur 8 jam.', 'Medium', '2026-07-25 20:38:31'),
(13, 'Risiko Penyakit Jantung', 'Segera Periksa Dokter', 'Terdapat indikasi risiko penyakit jantung.', 'Ikuti pola makan DASH sesuai anjuran tenaga kesehatan.', 'Hanya lakukan aktivitas sesuai saran dokter.', '2 liter air.', 'Tidur cukup.', 'Emergency', '2026-07-25 20:38:31'),
(14, 'Gangguan Pernapasan', 'Cari Bantuan Medis', 'SpO₂ berada pada tingkat berbahaya.', 'Ikuti arahan tenaga medis.', 'Hentikan aktivitas fisik.', 'Minum sesuai kebutuhan dan kondisi.', 'Istirahat total.', 'Emergency', '2026-07-25 20:38:31'),
(15, 'Kondisi Kritis', 'Segera Cari Pertolongan Medis', 'Parameter vital menunjukkan kondisi darurat.', 'Ikuti instruksi tenaga medis.', 'Jangan melakukan aktivitas.', 'Sesuai arahan tenaga medis.', 'Segera menuju fasilitas kesehatan.', 'Emergency', '2026-07-25 20:38:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `health_records`
--

CREATE TABLE `health_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `systolic` int(11) DEFAULT NULL,
  `diastolic` int(11) DEFAULT NULL,
  `spo2` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `hydration` int(11) DEFAULT NULL,
  `sleep_hours` decimal(4,1) DEFAULT NULL,
  `steps` int(11) DEFAULT NULL,
  `bmi` decimal(4,2) DEFAULT NULL,
  `health_score` int(11) DEFAULT NULL,
  `character_level` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `health_records`
--

INSERT INTO `health_records` (`id`, `user_id`, `heart_rate`, `systolic`, `diastolic`, `spo2`, `temperature`, `hydration`, `sleep_hours`, `steps`, `bmi`, `health_score`, `character_level`, `created_at`) VALUES
(7, 1, 70, 120, 80, 99, 36.6, 95, 8.0, 10000, 21.45, 100, 1, '2026-07-26 18:16:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `health_rules`
--

CREATE TABLE `health_rules` (
  `id` int(11) NOT NULL,
  `parameter_name` varchar(100) DEFAULT NULL,
  `min_value` decimal(10,2) DEFAULT NULL,
  `max_value` decimal(10,2) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `health_rules`
--

INSERT INTO `health_rules` (`id`, `parameter_name`, `min_value`, `max_value`, `score`, `status`, `description`) VALUES
(1, 'Heart Rate', 60.00, 100.00, 100, 'Healthy', 'Detak jantung normal saat istirahat.'),
(2, 'Heart Rate', 50.00, 59.00, 85, 'Normal', 'Sedikit lebih rendah, dapat terjadi pada atlet atau orang yang bugar.'),
(3, 'Heart Rate', 101.00, 110.00, 80, 'Normal', 'Sedikit meningkat, bisa dipengaruhi aktivitas, stres, atau kafein.'),
(4, 'Heart Rate', 45.00, 49.00, 60, 'Warning', 'Detak jantung rendah, perlu dipantau.'),
(5, 'Heart Rate', 111.00, 130.00, 60, 'Warning', 'Detak jantung tinggi, sebaiknya istirahat dan evaluasi penyebabnya.'),
(6, 'Heart Rate', 30.00, 44.00, 30, 'Critical', 'Bradikardia berat, perlu pemeriksaan medis segera.'),
(7, 'Heart Rate', 131.00, 220.00, 20, 'Critical', 'Takikardia berat, segera cari pertolongan medis.'),
(8, 'Systolic', 90.00, 120.00, 100, 'Healthy', 'Tekanan darah sistolik normal.'),
(9, 'Systolic', 121.00, 129.00, 90, 'Normal', 'Sedikit meningkat.'),
(10, 'Systolic', 130.00, 139.00, 70, 'Warning', 'Hipertensi tahap 1.'),
(11, 'Systolic', 140.00, 180.00, 40, 'Critical', 'Hipertensi tahap 2.'),
(12, 'Systolic', 181.00, 300.00, 10, 'Critical', 'Krisis hipertensi.'),
(13, 'Systolic', 70.00, 89.00, 60, 'Warning', 'Tekanan darah rendah.'),
(14, 'Systolic', 0.00, 69.00, 20, 'Critical', 'Hipotensi berat.'),
(15, 'Diastolic', 60.00, 80.00, 100, 'Healthy', 'Tekanan darah diastolik normal.'),
(16, 'Diastolic', 81.00, 89.00, 80, 'Warning', 'Sedikit meningkat.'),
(17, 'Diastolic', 90.00, 120.00, 40, 'Critical', 'Hipertensi.'),
(18, 'Diastolic', 40.00, 59.00, 60, 'Warning', 'Hipotensi ringan.'),
(19, 'Diastolic', 0.00, 39.00, 20, 'Critical', 'Hipotensi berat.'),
(20, 'SpO2', 95.00, 100.00, 100, 'Healthy', 'Saturasi oksigen normal.'),
(21, 'SpO2', 93.00, 94.00, 80, 'Normal', 'Sedikit menurun.'),
(22, 'SpO2', 90.00, 92.00, 60, 'Warning', 'Hipoksia ringan.'),
(23, 'SpO2', 85.00, 89.00, 30, 'Critical', 'Hipoksia sedang.'),
(24, 'SpO2', 0.00, 84.00, 10, 'Critical', 'Hipoksia berat.'),
(25, 'Hydration', 80.00, 100.00, 100, 'Healthy', 'Hidrasi tubuh baik.'),
(26, 'Hydration', 65.00, 79.00, 80, 'Normal', 'Mulai kurang cairan.'),
(27, 'Hydration', 50.00, 64.00, 60, 'Warning', 'Dehidrasi ringan.'),
(28, 'Hydration', 30.00, 49.00, 30, 'Critical', 'Dehidrasi sedang.'),
(29, 'Hydration', 0.00, 29.00, 10, 'Critical', 'Dehidrasi berat.'),
(30, 'Sleep', 7.00, 9.00, 100, 'Healthy', 'Durasi tidur ideal.'),
(31, 'Sleep', 6.00, 6.90, 85, 'Normal', 'Tidur sedikit kurang.'),
(32, 'Sleep', 5.00, 5.90, 60, 'Warning', 'Kurang tidur.'),
(33, 'Sleep', 0.00, 4.90, 20, 'Critical', 'Kurang tidur berat.'),
(34, 'Sleep', 9.10, 11.00, 80, 'Normal', 'Tidur lebih lama dari rata-rata.'),
(35, 'Sleep', 11.10, 24.00, 50, 'Warning', 'Tidur berlebihan, perlu evaluasi.'),
(36, 'Steps', 10000.00, 30000.00, 100, 'Healthy', 'Aktivitas fisik sangat baik.'),
(37, 'Steps', 7000.00, 9999.00, 85, 'Normal', 'Aktivitas fisik cukup.'),
(38, 'Steps', 5000.00, 6999.00, 70, 'Normal', 'Aktivitas sedang.'),
(39, 'Steps', 3000.00, 4999.00, 50, 'Warning', 'Kurang aktif.'),
(40, 'Steps', 0.00, 2999.00, 20, 'Critical', 'Sangat kurang aktivitas.'),
(41, 'BMI', 18.50, 24.90, 100, 'Healthy', 'Berat badan ideal.'),
(42, 'BMI', 25.00, 29.90, 75, 'Warning', 'Kelebihan berat badan.'),
(43, 'BMI', 30.00, 50.00, 40, 'Critical', 'Obesitas.'),
(44, 'BMI', 17.00, 18.40, 70, 'Warning', 'Berat badan kurang.'),
(45, 'BMI', 0.00, 16.90, 30, 'Critical', 'Kekurangan berat badan berat.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sos_contacts`
--

CREATE TABLE `sos_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_type` enum('keluarga','rumah_sakit','ambulans') NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sos_contacts`
--

INSERT INTO `sos_contacts` (`id`, `user_id`, `contact_type`, `name`, `phone_number`, `created_at`) VALUES
(1, 1, 'keluarga', 'Ayah', '6285716541462', '2026-07-27 03:29:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `reset_token`, `reset_expiry`, `gender`, `birth_date`, `height`, `weight`, `profile_photo`, `created_at`) VALUES
(1, 'Muhamad Miftahul Ulum', 'ulumm3952@gmail.com', '$2y$10$k2SycjanD9y85G03GaX3B.s4jqE5gr8Ink5oL4m3UgNbtbQ0vLZRi', 'cd426b55cf0271d0cdbd37ced7c5edb597709e6314ec3c26fa10bf9d5c4baae6', '2026-07-27 21:18:06', 'Male', '2006-09-24', 170.00, 62.00, 'default.png', '2026-07-26 08:00:57'),
(2, 'Aditya Ramadhan Khiang', 'adit@gmail.com', '$2y$10$wWyOBb7bO6r4y7q6xuCQbOy/STbctnc3VFdo/xQG08V6akv3jfrqG', '498f4b2aa393ad3e8195caa9d5edc993a7c297e4b83bfeb2a4921be8c437f463', '2026-07-27 21:15:56', 'Male', '2005-03-12', 167.00, 60.00, 'default.png', '2026-07-27 09:37:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `character_levels`
--
ALTER TABLE `character_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `health_conditions`
--
ALTER TABLE `health_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `health_recommendations`
--
ALTER TABLE `health_recommendations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `health_records`
--
ALTER TABLE `health_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `health_rules`
--
ALTER TABLE `health_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sos_contacts`
--
ALTER TABLE `sos_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `character_levels`
--
ALTER TABLE `character_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `health_conditions`
--
ALTER TABLE `health_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `health_recommendations`
--
ALTER TABLE `health_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `health_records`
--
ALTER TABLE `health_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `health_rules`
--
ALTER TABLE `health_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `sos_contacts`
--
ALTER TABLE `sos_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `health_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
