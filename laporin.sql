-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 08 Des 2025 pada 01.09
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
(1, 'SIMPEG (Sistem Informasi Kepegawaian)', 'Aplikasi untuk mengelola data kepegawaian, absensi, dan cuti pegawai', 1, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(2, 'E-Office (Surat Menyurat)', 'Aplikasi untuk pengelolaan surat masuk, surat keluar, dan disposisi', 1, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(3, 'SIMKEU (Sistem Informasi Keuangan)', 'Aplikasi pengelolaan keuangan, anggaran, dan pelaporan', 1, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(4, 'SIPBJ (Sistem Informasi Pengadaan Barang/Jasa)', 'Aplikasi untuk pengelolaan pengadaan dan inventaris barang', 0, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(5, 'E-Presensi', 'Aplikasi absensi online pegawai', 0, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(6, 'Website Instansi', 'Website resmi instansi untuk informasi publik', 0, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(7, 'Portal Pegawai', 'Portal self-service untuk pegawai', 0, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(8, 'SIMPEG Mobile', 'Aplikasi mobile SIMPEG untuk Android/iOS', 0, 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32');

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

--
-- Dumping data untuk tabel `attachments`
--

INSERT INTO `attachments` (`id`, `complaint_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`) VALUES
(1, 1, 'abstract-template-background-white-and-bright-blue-squares-overlapping-with-halftone-and-texture-free-vector.jpg', 'uploads/complaints/1765087721_693519e932fd8.jpg', 'image/jpeg', 75533, 3, '2025-12-07 06:08:41'),
(2, 2, 'photo-1510987836583-e3fb9586c7b3.jpeg', 'uploads/complaints/1765095154_693536f255ef1.jpeg', 'image/jpeg', 413184, 3, '2025-12-07 08:12:34'),
(3, 7, 'Screenshot 2025-12-07 192026.png', 'uploads/complaints/1765106436_69356304eaab3.png', 'image/png', 171961, 3, '2025-12-07 11:20:36'),
(4, 8, 'Screenshot 2025-12-07 201029.png', 'uploads/complaints/1765109439_69356ebf2cb5a.png', 'image/png', 113003, 3, '2025-12-07 12:10:39'),
(5, 9, 'Screenshot 2025-12-07 215822.png', 'uploads/complaints/1765115910_6935880671d83.png', 'image/png', 130167, 3, '2025-12-07 13:58:30');

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
(1, 1, 3, 'Halo', 0, '2025-12-07 06:20:03'),
(2, 1, 3, 'Apakah error nya sudah di tangani ?', 0, '2025-12-07 06:20:36'),
(3, 1, 3, 'Makasih ya', 0, '2025-12-07 06:45:51'),
(4, 2, 3, 'Apakah bisa di percepat dalam pengerjaan', 0, '2025-12-07 08:14:54'),
(5, 1, 3, 'Tst', 0, '2025-12-07 09:25:45'),
(6, 6, 5, 'Tolong diperbaiki', 0, '2025-12-07 10:21:28'),
(7, 6, 2, 'Baik segera kita cek', 0, '2025-12-07 10:21:53'),
(8, 6, 5, 'terima kasih', 0, '2025-12-07 10:22:41'),
(9, 6, 2, 'Tst', 1, '2025-12-07 10:23:07'),
(10, 6, 2, 'Silahkan di cek kembali', 0, '2025-12-07 10:23:55'),
(11, 7, 3, 'Bisa tolong di percepat ?', 0, '2025-12-07 11:21:50'),
(12, 7, 2, 'Silahkan di tunggu yakak', 0, '2025-12-07 11:22:17'),
(13, 7, 2, 'silahan di cek kembali ya kak', 0, '2025-12-07 11:23:38'),
(14, 8, 3, 'Apakah bisa di percepat dalam pengerjaan', 0, '2025-12-07 12:11:37'),
(15, 8, 2, 'Akan kita cek terlebih dahulu', 0, '2025-12-07 12:11:54'),
(16, 9, 3, 'Dipercepat', 0, '2025-12-07 14:09:52'),
(17, 9, 2, 'segera kami cek', 0, '2025-12-07 14:10:13');

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
(1, 3, 1, 1, 'Error saat login', 'bla bla bla bla', 'other', 'normal', 'closed', 2, '2025-12-07 14:40:59', '2025-12-07 06:42:45', '2025-12-07 06:08:40', '2025-12-07 06:42:45'),
(2, 3, 6, 4, 'Error saat mengupload data', 'Jadi BLA BLA BLA BLA BLA BLA', 'specific_bug', 'normal', 'closed', 2, '2025-12-07 16:13:46', '2025-12-07 16:17:15', '2025-12-07 08:12:34', '2025-12-07 08:16:55'),
(3, 5, 7, 5, 'Saran agar mempermudah kinjerja', 'Jadi BLABLABALBALBALBA', 'other', 'normal', 'closed', 2, '2025-12-07 17:23:23', '2025-12-07 10:31:54', '2025-12-07 09:18:04', '2025-12-07 10:31:54'),
(4, 4, 2, 1, 'Tidak bisa Login', 'Jadi BLABLABLABLABLABLA', 'cannot_use', 'urgent', 'closed', 2, '2025-12-07 17:55:52', '2025-12-07 10:03:56', '2025-12-07 09:55:06', '2025-12-07 10:03:56'),
(5, 3, 5, 4, 'Data Tidak bisa di Upload', 'Jadi BLABLABLABLABLA', 'specific_bug', 'urgent', 'closed', 2, '2025-12-07 10:15:32', '2025-12-07 10:15:41', '2025-12-07 10:12:32', '2025-12-07 10:28:11'),
(6, 5, 2, 4, 'Tidak bisa upload data', 'Jadi BLABLABLABLABLABLA', 'specific_bug', 'important', 'closed', 2, '2025-12-07 10:23:38', '2025-12-07 10:24:22', '2025-12-07 10:20:43', '2025-12-07 10:24:22'),
(7, 3, 2, 4, 'Error Login', 'jadi Blababla', 'cannot_use', 'urgent', 'closed', 2, '2025-12-07 11:23:26', '2025-12-07 11:24:23', '2025-12-07 11:20:36', '2025-12-07 11:24:23'),
(8, 3, 5, 4, 'Data Error', 'Jadi BLABLABLA', 'specific_bug', 'normal', 'closed', 2, '2025-12-07 12:12:19', '2025-12-07 12:12:39', '2025-12-07 12:10:39', '2025-12-07 12:12:39'),
(9, 3, 3, 5, 'Tidak bisa login', 'Jadi BLABLABLA', 'specific_bug', 'important', 'closed', 2, '2025-12-07 14:11:39', '2025-12-07 14:12:35', '2025-12-07 13:58:30', '2025-12-07 14:12:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `complaint_history`
--

DROP TABLE IF EXISTS `complaint_history`;
CREATE TABLE `complaint_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `complaint_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL COMMENT 'created, status_changed, priority_changed, assigned, etc',
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `complaint_history`
--

INSERT INTO `complaint_history` (`id`, `complaint_id`, `user_id`, `action`, `old_value`, `new_value`, `description`, `created_at`) VALUES
(1, 1, 3, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: urgent', '2025-12-07 06:08:41'),
(2, 1, 3, 'updated', NULL, NULL, 'Laporan diupdate oleh user', '2025-12-07 06:15:25'),
(3, 1, 3, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-07 06:42:45'),
(4, 2, 3, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: normal', '2025-12-07 08:12:34'),
(5, 2, 3, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-07 08:16:55'),
(6, 3, 5, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: normal', '2025-12-07 09:18:04'),
(7, 3, 5, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 1/5)', '2025-12-07 09:24:09'),
(8, 4, 4, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: urgent', '2025-12-07 09:55:06'),
(9, 4, 4, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-07 10:03:56'),
(10, 5, 3, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: normal', '2025-12-07 10:12:32'),
(11, 5, 2, 'assigned', NULL, 'Administrator', 'Laporan ditugaskan ke Administrator', '2025-12-07 10:13:08'),
(12, 5, 2, 'status_changed', 'in_progress', 'resolved', 'Status diubah dari in_progress menjadi resolved', '2025-12-07 10:15:32'),
(13, 5, 3, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-07 10:15:41'),
(14, 6, 5, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: important', '2025-12-07 10:20:43'),
(15, 6, 2, 'assigned', NULL, 'Administrator', 'Laporan ditugaskan ke Administrator', '2025-12-07 10:21:06'),
(16, 6, 2, 'status_changed', 'in_progress', 'resolved', 'Status diubah dari in_progress menjadi resolved', '2025-12-07 10:23:38'),
(17, 6, 5, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 4/5)', '2025-12-07 10:24:22'),
(18, 5, 2, 'priority_changed', 'normal', 'urgent', 'Prioritas diubah dari normal menjadi urgent oleh admin', '2025-12-07 10:28:11'),
(19, 3, 2, 'status_changed', 'closed', 'pending', 'Status diubah dari closed menjadi pending', '2025-12-07 10:31:29'),
(20, 3, 2, 'status_changed', 'pending', 'closed', 'Status diubah dari pending menjadi closed', '2025-12-07 10:31:54'),
(21, 7, 3, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: urgent', '2025-12-07 11:20:36'),
(22, 7, 2, 'assigned', NULL, 'Administrator', 'Laporan ditugaskan ke Administrator', '2025-12-07 11:21:17'),
(23, 7, 2, 'status_changed', 'in_progress', 'resolved', 'Status diubah dari in_progress menjadi resolved', '2025-12-07 11:23:26'),
(24, 7, 3, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 4/5)', '2025-12-07 11:24:23'),
(25, 8, 3, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: normal', '2025-12-07 12:10:39'),
(26, 8, 2, 'assigned', NULL, 'Administrator', 'Laporan ditugaskan ke Administrator', '2025-12-07 12:11:20'),
(27, 8, 2, 'status_changed', 'in_progress', 'resolved', 'Status diubah dari in_progress menjadi resolved', '2025-12-07 12:12:19'),
(28, 8, 3, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-07 12:12:39'),
(29, 9, 3, 'created', NULL, NULL, 'Laporan dibuat dengan prioritas: important', '2025-12-07 13:58:30'),
(30, 9, 2, 'assigned', NULL, 'Administrator', 'Laporan ditugaskan ke Administrator', '2025-12-07 13:59:23'),
(31, 9, 2, 'status_changed', 'in_progress', 'resolved', 'Status diubah dari in_progress menjadi resolved', '2025-12-07 14:11:39'),
(32, 9, 3, 'closed', 'resolved', 'closed', 'Laporan ditutup setelah user memberikan feedback (Rating: 5/5)', '2025-12-07 14:12:35');

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
(1, 1, 3, 5, 'Keren', '2025-12-07 06:42:45'),
(2, 2, 3, 5, 'Dapat di Andalkan\r\nAplikasi dapat berjalan dengan baik', '2025-12-07 08:16:55'),
(3, 3, 5, 1, 'Terlalu Lama respon', '2025-12-07 09:24:09'),
(4, 4, 4, 5, '', '2025-12-07 10:03:56'),
(5, 5, 3, 5, '', '2025-12-07 10:15:41'),
(6, 6, 5, 4, '', '2025-12-07 10:24:22'),
(7, 7, 3, 4, 'Penanganan yang cepat', '2025-12-07 11:24:23'),
(8, 8, 3, 5, 'Sangat Baik', '2025-12-07 12:12:39'),
(9, 9, 3, 5, 'Sangat Baik\r\n', '2025-12-07 14:12:35');

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
(10, '2025-12-06-185329', 'App\\Database\\Migrations\\CreateFeedbacksTable', 'default', 'App', 1765047487, 1);

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
(1, 2, 1, 'Laporan Baru', 'Laporan baru telah dibuat: Error saat login', 0, '2025-12-07 06:08:41'),
(2, 1, 1, 'Laporan Baru', 'Laporan baru telah dibuat: Error saat login', 0, '2025-12-07 06:08:41'),
(3, 2, 1, 'Pesan Baru dari User', 'User Demo 1 mengirim pesan pada laporan #1', 0, '2025-12-07 06:20:03'),
(4, 1, 1, 'Pesan Baru dari User', 'User Demo 1 mengirim pesan pada laporan #1', 0, '2025-12-07 06:20:03'),
(5, 2, 1, 'Pesan Baru dari User', 'User Demo 1 mengirim pesan pada laporan #1', 0, '2025-12-07 06:20:36'),
(6, 1, 1, 'Pesan Baru dari User', 'User Demo 1 mengirim pesan pada laporan #1', 0, '2025-12-07 06:20:36'),
(7, 2, 1, 'Pesan Baru', 'User Demo 1 mengirim pesan baru pada laporan Anda', 0, '2025-12-07 06:45:51'),
(8, 2, 2, 'Laporan Baru', 'Laporan baru telah dibuat: Error saat mengupload data', 0, '2025-12-07 08:12:34'),
(9, 1, 2, 'Laporan Baru', 'Laporan baru telah dibuat: Error saat mengupload data', 0, '2025-12-07 08:12:34'),
(10, 2, 2, 'Pesan Baru', 'User Demo 1 mengirim pesan baru pada laporan Anda', 0, '2025-12-07 08:14:54'),
(11, 2, 3, 'Laporan Baru', 'Laporan baru telah dibuat: Saran agar mempermudah kinjerja', 0, '2025-12-07 09:18:04'),
(12, 1, 3, 'Laporan Baru', 'Laporan baru telah dibuat: Saran agar mempermudah kinjerja', 0, '2025-12-07 09:18:04'),
(13, 2, 1, 'Pesan Baru', 'User Demo 1 mengirim pesan baru pada laporan Anda', 0, '2025-12-07 09:25:45'),
(14, 2, 4, 'Laporan Baru', 'Laporan baru telah dibuat: Tidak bisa Login', 0, '2025-12-07 09:55:06'),
(15, 1, 4, 'Laporan Baru', 'Laporan baru telah dibuat: Tidak bisa Login', 0, '2025-12-07 09:55:06'),
(16, 2, 5, 'Laporan Baru', 'Laporan baru telah dibuat: Data Tidak bisa di Upload', 0, '2025-12-07 10:12:32'),
(17, 1, 5, 'Laporan Baru', 'Laporan baru telah dibuat: Data Tidak bisa di Upload', 0, '2025-12-07 10:12:32'),
(18, 3, 5, 'Laporan Ditangani', 'Laporan Anda sedang ditangani oleh Administrator', 0, '2025-12-07 10:13:08'),
(19, 3, 5, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Sedang Diproses menjadi Selesai', 0, '2025-12-07 10:15:32'),
(20, 3, 5, 'Laporan Selesai', 'Laporan Anda telah diselesaikan. Silakan berikan feedback.', 0, '2025-12-07 10:15:32'),
(21, 2, 6, 'Laporan Baru', 'Laporan baru telah dibuat: Tidak bisa upload data', 0, '2025-12-07 10:20:43'),
(22, 1, 6, 'Laporan Baru', 'Laporan baru telah dibuat: Tidak bisa upload data', 0, '2025-12-07 10:20:43'),
(23, 5, 6, 'Laporan Ditangani', 'Laporan Anda sedang ditangani oleh Administrator', 0, '2025-12-07 10:21:06'),
(24, 2, 6, 'Pesan Baru', 'Users Demo mengirim pesan baru pada laporan Anda', 0, '2025-12-07 10:21:28'),
(25, 5, 6, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-07 10:21:53'),
(26, 2, 6, 'Pesan Baru', 'Users Demo mengirim pesan baru pada laporan Anda', 0, '2025-12-07 10:22:41'),
(27, 5, 6, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Sedang Diproses menjadi Selesai', 0, '2025-12-07 10:23:38'),
(28, 5, 6, 'Laporan Selesai', 'Laporan Anda telah diselesaikan. Silakan berikan feedback.', 0, '2025-12-07 10:23:38'),
(29, 5, 6, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-07 10:23:55'),
(30, 5, 3, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Ditutup menjadi Pending', 0, '2025-12-07 10:31:29'),
(31, 5, 3, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Pending menjadi Ditutup', 0, '2025-12-07 10:31:54'),
(32, 2, 7, 'Laporan Baru', 'Laporan baru telah dibuat: Error Login', 0, '2025-12-07 11:20:36'),
(33, 1, 7, 'Laporan Baru', 'Laporan baru telah dibuat: Error Login', 0, '2025-12-07 11:20:36'),
(34, 3, 7, 'Laporan Ditangani', 'Laporan Anda sedang ditangani oleh Administrator', 0, '2025-12-07 11:21:17'),
(35, 2, 7, 'Pesan Baru', 'User Demo 1 mengirim pesan baru pada laporan Anda', 0, '2025-12-07 11:21:50'),
(36, 3, 7, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-07 11:22:17'),
(37, 3, 7, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Sedang Diproses menjadi Selesai', 0, '2025-12-07 11:23:26'),
(38, 3, 7, 'Laporan Selesai', 'Laporan Anda telah diselesaikan. Silakan berikan feedback.', 0, '2025-12-07 11:23:26'),
(39, 3, 7, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-07 11:23:38'),
(40, 2, 8, 'Laporan Baru', 'Laporan baru telah dibuat: Data Error', 0, '2025-12-07 12:10:39'),
(41, 1, 8, 'Laporan Baru', 'Laporan baru telah dibuat: Data Error', 0, '2025-12-07 12:10:39'),
(42, 3, 8, 'Laporan Ditangani', 'Laporan Anda sedang ditangani oleh Administrator', 0, '2025-12-07 12:11:20'),
(43, 2, 8, 'Pesan Baru', 'User Demo 1 mengirim pesan baru pada laporan Anda', 0, '2025-12-07 12:11:37'),
(44, 3, 8, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-07 12:11:54'),
(45, 3, 8, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Sedang Diproses menjadi Selesai', 0, '2025-12-07 12:12:19'),
(46, 3, 8, 'Laporan Selesai', 'Laporan Anda telah diselesaikan. Silakan berikan feedback.', 0, '2025-12-07 12:12:19'),
(47, 2, 9, 'Laporan Baru', 'Laporan baru telah dibuat: Tidak bisa login', 0, '2025-12-07 13:58:30'),
(48, 1, 9, 'Laporan Baru', 'Laporan baru telah dibuat: Tidak bisa login', 0, '2025-12-07 13:58:30'),
(49, 3, 9, 'Laporan Ditangani', 'Laporan Anda sedang ditangani oleh Administrator', 0, '2025-12-07 13:59:23'),
(50, 2, 9, 'Pesan Baru', 'User Demo 1 mengirim pesan baru pada laporan Anda', 0, '2025-12-07 14:09:52'),
(51, 3, 9, 'Pesan Baru', 'Administrator mengirim pesan baru pada laporan Anda', 0, '2025-12-07 14:10:13'),
(52, 3, 9, 'Status Laporan Berubah', 'Status laporan Anda berubah dari Sedang Diproses menjadi Selesai', 0, '2025-12-07 14:11:39'),
(53, 3, 9, 'Laporan Selesai', 'Laporan Anda telah diselesaikan. Silakan berikan feedback.', 0, '2025-12-07 14:11:39');

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
(3, 'user1', 'user1@example.com', '$2y$10$h5BfZ16Up6Pe5C/0Ep5hUeNU8a4S2Lj56nu9awTd4MmBAOeuWJC.6', 'User Demo 1', 'user', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(4, 'user2', 'user2@example.com', '$2y$10$7l1C6i8Ncqdw1U4n5pewTuI1wADiUTT3T1Hov5aBVKGk3NmV3CUrG', 'User Demo 2', 'user', 1, '2025-12-06 19:05:32', '2025-12-06 19:05:32'),
(5, 'users2', 'users2@gmail.com', '$2y$10$roPs6pSR4t8RfzsQnXySEOwyj0120kCWqXKgu6b.82r2J9LxPtrUm', 'Users Demo', 'user', 1, '2025-12-07 09:13:54', '2025-12-07 09:13:54');

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `complaint_history`
--
ALTER TABLE `complaint_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `knowledge_base`
--
ALTER TABLE `knowledge_base`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
