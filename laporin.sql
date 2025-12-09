-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 09 Des 2025 pada 12.02
-- Versi server: 11.4.2-MariaDB-log
-- Versi PHP: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laporin`
--
CREATE DATABASE IF NOT EXISTS `laporin` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `laporin`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE `applications` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_critical` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Aplikasi kritikal akan meningkatkan prioritas laporan',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `applications`
--

INSERT INTO `applications` (`id`, `name`, `description`, `is_critical`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 'Penilaian', '', 0, 1, '2025-12-09 05:42:18', '2025-12-09 11:56:14'),
(11, 'Perjadin', '', 0, 1, '2025-12-09 05:42:27', '2025-12-09 11:56:21'),
(12, 'Pengaduan', '', 0, 1, '2025-12-09 05:42:39', '2025-12-09 11:56:08'),
(13, 'Sistem Statistik Tepadu', '', 0, 1, '2025-12-09 05:42:59', '2025-12-09 11:56:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (
  `id` int(11) UNSIGNED NOT NULL,
  `complaint_id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Error System', 'Error atau bug pada sistem yang menyebabkan aplikasi tidak berfungsi', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(2, 'Performance Issue', 'Masalah performa seperti loading lambat atau aplikasi lemot', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(3, 'Login Problem', 'Masalah saat login atau autentikasi', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(4, 'Data Issue', 'Masalah terkait data tidak muncul, data hilang, atau data tidak akurat', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(5, 'Feature Request', 'Permintaan fitur baru atau penyempurnaan fitur yang ada', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(6, 'UI/UX Issue', 'Masalah tampilan atau pengalaman pengguna', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(7, 'Integration Problem', 'Masalah integrasi antar sistem', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(8, 'Security Issue', 'Masalah keamanan atau akses tidak authorized', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(9, 'Lainnya', 'Kategori lainnya yang tidak masuk dalam kategori di atas', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE `chats` (
  `id` int(11) UNSIGNED NOT NULL,
  `complaint_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_internal_note` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Catatan internal admin (tidak terlihat user)',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `chats`
--

INSERT INTO `chats` (`id`, `complaint_id`, `user_id`, `message`, `is_internal_note`, `created_at`) VALUES
(32, 21, 7, 'Tolong di bantu arahannya', 0, '2025-12-09 11:58:25'),
(33, 21, 2, 'Silahkan di cek kembali', 0, '2025-12-09 12:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE `complaints` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `application_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `impact_type` enum('cannot_use','specific_bug','slow_performance','other') NOT NULL COMMENT 'User pilih dampak, sistem tentukan prioritas',
  `priority` enum('normal','important','urgent') NOT NULL DEFAULT 'normal' COMMENT 'Auto-calculated by system, dapat di-override admin',
  `status` enum('pending','in_progress','resolved','closed') NOT NULL DEFAULT 'pending',
  `assigned_to` int(11) UNSIGNED DEFAULT NULL COMMENT 'Admin yang handle laporan ini',
  `resolved_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `application_id`, `category_id`, `title`, `description`, `impact_type`, `priority`, `status`, `assigned_to`, `resolved_at`, `closed_at`, `created_at`, `updated_at`) VALUES
(21, 7, 13, 4, 'Gagal Mengapload Data', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, urna eget tincidunt tempus, augue tortor cursus sapien, eget molestie justo lorem sed nisl. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Integer dictum magna id velit efficitur, eu sagittis arcu feugiat. Donec at tortor nec magna facilisis malesuada. Mauris sodales justo eget mi efficitur, sed viverra urna mattis. Suspendisse potenti. Praesent euismod nisl in dapibus tristique, at fermentum neque dapibus.', 'specific_bug', 'normal', 'closed', 2, '2025-12-09 11:59:15', '2025-12-09 12:01:00', '2025-12-09 11:57:44', '2025-12-09 12:01:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `complaint_history`
--

DROP TABLE IF EXISTS `complaint_history`;
CREATE TABLE `complaint_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `complaint_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'created, status_changed, priority_changed, assigned, etc',
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `complaint_history`
--

INSERT INTO `complaint_history` (`id`, `complaint_id`, `user_id`, `user_name`, `user_email`, `action`, `old_value`, `new_value`, `description`, `created_at`) VALUES
(69, 21, 7, 'Demo Account', 'demo@example.com', 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: normal', '2025-12-09 11:57:45'),
(70, 21, 2, 'Administrator', 'admin@example.com', 'assigned', NULL, 'Administrator', 'Laporan ditugaskan ke Administrator', '2025-12-09 11:58:57'),
(71, 21, 2, 'Administrator', 'admin@example.com', 'status_changed', 'in_progress', 'resolved', 'Status diubah dari in_progress menjadi resolved', '2025-12-09 11:59:15'),
(72, 21, 7, 'Demo Account', 'demo@example.com', 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-09 12:01:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE `feedbacks` (
  `id` int(11) UNSIGNED NOT NULL,
  `complaint_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `rating` tinyint(1) NOT NULL COMMENT 'Rating 1-5',
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `complaint_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(16, 21, 7, 5, 'Menangani masalah dengan cepat', '2025-12-09 12:01:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `knowledge_base`
--

DROP TABLE IF EXISTS `knowledge_base`;
CREATE TABLE `knowledge_base` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `application_id` int(11) UNSIGNED DEFAULT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL COMMENT 'Comma separated tags',
  `view_count` int(11) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `knowledge_base`
--

INSERT INTO `knowledge_base` (`id`, `title`, `content`, `application_id`, `category_id`, `tags`, `view_count`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Cara Reset Password Akun', '<h3>Lupa Password?</h3><p>Jika Anda lupa password, silakan hubungi administrator IT untuk reset password. Jangan bagikan password Anda kepada siapapun.</p><p><strong>Langkah-langkah:</strong></p><ol><li>Hubungi admin IT via email atau telepon</li><li>Verifikasi identitas Anda</li><li>Admin akan mengirim password baru ke email Anda</li><li>Login dan segera ubah password</li></ol>', NULL, 3, 'password,reset,login', 5, 1, 1, '2025-12-06 19:05:32', '2025-12-07 08:23:01'),
(2, 'Aplikasi Lambat? Coba Cara Ini', '<h3>Tips Mengatasi Aplikasi Lambat</h3><p>Jika aplikasi terasa lambat, coba langkah berikut:</p><ul><li><strong>Clear Cache Browser:</strong> Tekan Ctrl+Shift+Delete dan hapus cache</li><li><strong>Tutup Tab yang Tidak Digunakan:</strong> Terlalu banyak tab membuat browser lambat</li><li><strong>Cek Koneksi Internet:</strong> Pastikan koneksi internet stabil</li><li><strong>Restart Browser:</strong> Tutup dan buka kembali browser</li><li><strong>Update Browser:</strong> Gunakan browser versi terbaru</li></ul><p>Jika masih lambat, laporkan ke admin IT.</p>', NULL, 2, 'performance,lambat,slow,cache', 4, 1, 1, '2025-12-06 19:05:32', '2025-12-07 12:30:12'),
(3, 'Cara Upload File dengan Benar', '<h3>Panduan Upload File</h3><p><strong>Format File yang Didukung:</strong></p><ul><li>Gambar: JPG, PNG, GIF (max 5MB)</li><li>Dokumen: PDF, DOC, DOCX, XLS, XLSX (max 10MB)</li><li>Video: MP4, AVI (max 50MB)</li></ul><p><strong>Cara Upload:</strong></p><ol><li>Klik tombol \"Pilih File\" atau \"Browse\"</li><li>Pilih file dari komputer Anda</li><li>Tunggu hingga progress bar selesai</li><li>Jangan tutup halaman saat upload</li></ol><p><strong>Troubleshooting:</strong></p><ul><li>Jika gagal, cek ukuran file</li><li>Pastikan format file sesuai</li><li>Coba gunakan koneksi internet yang lebih stabil</li></ul>', NULL, NULL, 'upload,file,attachment', 1, 1, 1, '2025-12-06 19:05:32', '2025-12-07 01:15:24'),
(4, 'Error 404 Not Found', '<h3>Apa itu Error 404?</h3><p>Error 404 berarti halaman yang Anda cari tidak ditemukan.</p><p><strong>Penyebab Umum:</strong></p><ul><li>URL salah atau typo</li><li>Halaman sudah dihapus atau dipindahkan</li><li>Link broken dari sumber lain</li></ul><p><strong>Solusi:</strong></p><ol><li>Cek kembali URL yang Anda ketik</li><li>Kembali ke halaman utama</li><li>Gunakan fitur search</li><li>Hubungi admin jika masalah berlanjut</li></ol>', NULL, 1, 'error,404,not found', 9, 1, 1, '2025-12-06 19:05:32', '2025-12-07 08:22:57'),
(5, 'Tips Keamanan Akun', '<h3>Jaga Keamanan Akun Anda</h3><p><strong>Password yang Aman:</strong></p><ul><li>Minimal 8 karakter</li><li>Kombinasi huruf besar, kecil, angka, dan simbol</li><li>Jangan gunakan tanggal lahir atau nama</li><li>Ubah password secara berkala</li></ul><p><strong>Jangan Pernah:</strong></p><ul><li>Berbagi password dengan orang lain</li><li>Tulis password di tempat yang mudah dilihat</li><li>Gunakan password yang sama untuk semua akun</li><li>Login dari komputer publik tanpa logout</li></ul><p><strong>Jika Akun Diretas:</strong></p><ol><li>Segera hubungi admin IT</li><li>Ganti password semua akun terkait</li><li>Laporkan aktivitas mencurigakan</li></ol>', NULL, 8, 'security,password,keamanan', 1, 1, 1, '2025-12-06 19:05:32', '2025-12-07 01:27:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-12-06-184923', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1765047487, 1),
(2, '2025-12-06-185028', 'App\\Database\\Migrations\\CreateApplicationsTable', 'default', 'App', 1765047487, 1),
(3, '2025-12-06-185057', 'App\\Database\\Migrations\\CreateCategoriesTable', 'default', 'App', 1765047487, 1),
(4, '2025-12-06-185120', 'App\\Database\\Migrations\\CreateComplaintsTable', 'default', 'App', 1765047487, 1),
(5, '2025-12-06-185143', 'App\\Database\\Migrations\\CreateAttachmentsTable', 'default', 'App', 1765047487, 1),
(6, '2025-12-06-185206', 'App\\Database\\Migrations\\CreateChatsTable', 'default', 'App', 1765047487, 1),
(7, '2025-12-06-185227', 'App\\Database\\Migrations\\CreateComplaintHistoryTable', 'default', 'App', 1765047487, 1),
(8, '2025-12-06-185252', 'App\\Database\\Migrations\\CreateKnowledgeBaseTable', 'default', 'App', 1765047487, 1),
(9, '2025-12-06-185311', 'App\\Database\\Migrations\\CreateNotificationsTable', 'default', 'App', 1765047487, 1),
(10, '2025-12-06-185329', 'App\\Database\\Migrations\\CreateFeedbacksTable', 'default', 'App', 1765047487, 1),
(11, '2025-12-08-120000', 'App\\Database\\Migrations\\AddUserFieldsToComplaintHistory', 'default', 'App', 1765160160, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `complaint_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `complaint_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(54, 2, NULL, 'Laporan Baru', 'Laporan baru telah dibuat: Data Error', 1, '2025-12-08 02:16:06'),
(55, 1, NULL, 'Laporan Baru', 'Laporan baru telah dibuat: Data Error', 1, '2025-12-08 02:16:06'),
(59, 2, NULL, 'Laporan Baru', 'Laporan baru telah dibuat: aasasasasfewffdsfdsf3121r', 1, '2025-12-08 04:10:03'),
(60, 1, NULL, 'Laporan Baru', 'Laporan baru telah dibuat: aasasasasfewffdsfdsf3121r', 1, '2025-12-08 04:10:03'),
(64, 2, NULL, 'Pesan Baru', 'User Demo 2 mengirim pesan baru pada laporan Anda', 1, '2025-12-08 04:13:02'),
(65, 2, NULL, 'Pesan Baru dari User', 'User Demo 2 mengirim pesan pada laporan #14', 1, '2025-12-08 04:15:00'),
(66, 1, NULL, 'Pesan Baru dari User', 'User Demo 2 mengirim pesan pada laporan #14', 1, '2025-12-08 04:15:00'),
(118, 2, 21, 'Laporan Baru', 'Laporan baru telah dibuat: Gagal Mengapload Data', 0, '2025-12-09 11:57:45'),
(119, 1, 21, 'Laporan Baru', 'Laporan baru telah dibuat: Gagal Mengapload Data', 0, '2025-12-09 11:57:45'),
(120, 2, 21, 'Pesan Baru dari User', 'Demo Account mengirim pesan pada laporan #21', 0, '2025-12-09 11:58:25'),
(121, 1, 21, 'Pesan Baru dari User', 'Demo Account mengirim pesan pada laporan #21', 0, '2025-12-09 11:58:25'),
(122, 7, 21, 'Laporan Ditangani', 'Laporan Anda sedang ditangani oleh Administrator', 0, '2025-12-09 11:58:57'),
(123, 7, 21, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Sedang Diproses menjadi Selesai', 0, '2025-12-09 11:59:15'),
(124, 7, 21, 'Laporan Selesai', 'Laporan Anda telah diselesaikan. Silakan berikan feedback.', 0, '2025-12-09 11:59:15'),
(125, 7, 21, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-09 12:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `search_history`
--

DROP TABLE IF EXISTS `search_history`;
CREATE TABLE `search_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `query` varchar(255) NOT NULL,
  `filters` text DEFAULT NULL,
  `results_count` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `search_history`
--

INSERT INTO `search_history` (`id`, `user_id`, `query`, `filters`, `results_count`, `created_at`) VALUES
(1, 2, 'belum', '[]', 0, '2025-12-08 05:21:25'),
(2, 2, 'belum', '[]', 0, '2025-12-08 05:22:19'),
(3, 2, 'belum', '[]', 0, '2025-12-08 05:22:22'),
(4, 2, 'belum', '[]', 0, '2025-12-08 05:23:00'),
(5, 2, 'belum', '[]', 0, '2025-12-08 05:23:14'),
(6, 2, 'belum', '[]', 0, '2025-12-08 05:23:33'),
(7, 2, 'belum', '[]', 0, '2025-12-08 05:28:06'),
(8, 2, 'Login', '[]', 6, '2025-12-08 05:28:15'),
(9, 2, 'Login', '[]', 6, '2025-12-08 05:29:10'),
(10, 2, 'Login', '[]', 6, '2025-12-08 05:29:16'),
(11, 2, 'a', '[]', 24, '2025-12-08 05:38:19'),
(12, 2, 'a', '[]', 24, '2025-12-08 05:39:11'),
(13, 2, 'a', '[]', 24, '2025-12-08 05:39:15'),
(14, 2, 'a', '[]', 24, '2025-12-08 05:41:19'),
(15, 2, 'a', '[]', 24, '2025-12-08 05:48:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('user','admin','superadmin') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'superadmin@example.com', '$2y$10$QVmg2FwnI0tbnNRmiTZaeuVq7VOMcCMu1mnX82Y7eiLYlfOixoEX6', 'Super Administrator', 'superadmin', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(2, 'admin', 'admin@example.com', '$2y$10$DLfQ7/Ef.PCtUMvt8Tl89OtgIDWSVjf8aIHwE2hQJRSrXFBGeiis2', 'Administrator', 'admin', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(7, 'Demo', 'demo@example.com', '$2y$10$53Rv1ylbN4pz5EkcuU31JOloQ9pRjPTnoj4I1mkaDVftb/mJHxgCS', 'Demo Account', 'user', 1, '2025-12-09 11:54:40', '2025-12-09 11:54:40');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_critical` (`is_critical`);

--
-- Indeks untuk tabel `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chats_user_id_foreign` (`user_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indeks untuk tabel `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaints_application_id_foreign` (`application_id`),
  ADD KEY `complaints_category_id_foreign` (`category_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `priority` (`priority`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indeks untuk tabel `complaint_history`
--
ALTER TABLE `complaint_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_history_user_id_foreign` (`user_id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indeks untuk tabel `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedbacks_user_id_foreign` (`user_id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indeks untuk tabel `knowledge_base`
--
ALTER TABLE `knowledge_base`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_base_category_id_foreign` (`category_id`),
  ADD KEY `knowledge_base_created_by_foreign` (`created_by`),
  ADD KEY `application_id` (`application_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_complaint_id_foreign` (`complaint_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Indeks untuk tabel `search_history`
--
ALTER TABLE `search_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `complaint_history`
--
ALTER TABLE `complaint_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT untuk tabel `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `knowledge_base`
--
ALTER TABLE `knowledge_base`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT untuk tabel `search_history`
--
ALTER TABLE `search_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `complaints_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `complaint_history`
--
ALTER TABLE `complaint_history`
  ADD CONSTRAINT `complaint_history_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaint_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedbacks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `knowledge_base`
--
ALTER TABLE `knowledge_base`
  ADD CONSTRAINT `knowledge_base_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `knowledge_base_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `knowledge_base_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `search_history`
--
ALTER TABLE `search_history`
  ADD CONSTRAINT `search_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
