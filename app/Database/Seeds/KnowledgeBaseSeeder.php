<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID user superadmin (created_by)
        $superadmin = $this->db->table('users')
            ->where('role', 'superadmin')
            ->get()
            ->getRow();

        if (!$superadmin) {
            echo "❌ Superadmin not found! Run UserSeeder first.\n";
            return;
        }

        $articles = [
            [
                'title'          => 'Cara Reset Password Akun',
                'content'        => '<h3>Lupa Password?</h3><p>Jika Anda lupa password, silakan hubungi administrator IT untuk reset password. Jangan bagikan password Anda kepada siapapun.</p><p><strong>Langkah-langkah:</strong></p><ol><li>Hubungi admin IT via email atau telepon</li><li>Verifikasi identitas Anda</li><li>Admin akan mengirim password baru ke email Anda</li><li>Login dan segera ubah password</li></ol>',
                'application_id' => null,
                'category_id'    => 3, // Login Problem
                'tags'           => 'password,reset,login',
                'view_count'     => 0,
                'is_published'   => 1,
                'created_by'     => $superadmin->id,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'title'          => 'Aplikasi Lambat? Coba Cara Ini',
                'content'        => '<h3>Tips Mengatasi Aplikasi Lambat</h3><p>Jika aplikasi terasa lambat, coba langkah berikut:</p><ul><li><strong>Clear Cache Browser:</strong> Tekan Ctrl+Shift+Delete dan hapus cache</li><li><strong>Tutup Tab yang Tidak Digunakan:</strong> Terlalu banyak tab membuat browser lambat</li><li><strong>Cek Koneksi Internet:</strong> Pastikan koneksi internet stabil</li><li><strong>Restart Browser:</strong> Tutup dan buka kembali browser</li><li><strong>Update Browser:</strong> Gunakan browser versi terbaru</li></ul><p>Jika masih lambat, laporkan ke admin IT.</p>',
                'application_id' => null,
                'category_id'    => 2, // Performance Issue
                'tags'           => 'performance,lambat,slow,cache',
                'view_count'     => 0,
                'is_published'   => 1,
                'created_by'     => $superadmin->id,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'title'          => 'Cara Upload File dengan Benar',
                'content'        => '<h3>Panduan Upload File</h3><p><strong>Format File yang Didukung:</strong></p><ul><li>Gambar: JPG, PNG, GIF (max 5MB)</li><li>Dokumen: PDF, DOC, DOCX, XLS, XLSX (max 10MB)</li><li>Video: MP4, AVI (max 50MB)</li></ul><p><strong>Cara Upload:</strong></p><ol><li>Klik tombol "Pilih File" atau "Browse"</li><li>Pilih file dari komputer Anda</li><li>Tunggu hingga progress bar selesai</li><li>Jangan tutup halaman saat upload</li></ol><p><strong>Troubleshooting:</strong></p><ul><li>Jika gagal, cek ukuran file</li><li>Pastikan format file sesuai</li><li>Coba gunakan koneksi internet yang lebih stabil</li></ul>',
                'application_id' => null,
                'category_id'    => null,
                'tags'           => 'upload,file,attachment',
                'view_count'     => 0,
                'is_published'   => 1,
                'created_by'     => $superadmin->id,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'title'          => 'Error 404 Not Found',
                'content'        => '<h3>Apa itu Error 404?</h3><p>Error 404 berarti halaman yang Anda cari tidak ditemukan.</p><p><strong>Penyebab Umum:</strong></p><ul><li>URL salah atau typo</li><li>Halaman sudah dihapus atau dipindahkan</li><li>Link broken dari sumber lain</li></ul><p><strong>Solusi:</strong></p><ol><li>Cek kembali URL yang Anda ketik</li><li>Kembali ke halaman utama</li><li>Gunakan fitur search</li><li>Hubungi admin jika masalah berlanjut</li></ol>',
                'application_id' => null,
                'category_id'    => 1, // Error System
                'tags'           => 'error,404,not found',
                'view_count'     => 0,
                'is_published'   => 1,
                'created_by'     => $superadmin->id,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'title'          => 'Tips Keamanan Akun',
                'content'        => '<h3>Jaga Keamanan Akun Anda</h3><p><strong>Password yang Aman:</strong></p><ul><li>Minimal 8 karakter</li><li>Kombinasi huruf besar, kecil, angka, dan simbol</li><li>Jangan gunakan tanggal lahir atau nama</li><li>Ubah password secara berkala</li></ul><p><strong>Jangan Pernah:</strong></p><ul><li>Berbagi password dengan orang lain</li><li>Tulis password di tempat yang mudah dilihat</li><li>Gunakan password yang sama untuk semua akun</li><li>Login dari komputer publik tanpa logout</li></ul><p><strong>Jika Akun Diretas:</strong></p><ol><li>Segera hubungi admin IT</li><li>Ganti password semua akun terkait</li><li>Laporkan aktivitas mencurigakan</li></ol>',
                'application_id' => null,
                'category_id'    => 8, // Security Issue
                'tags'           => 'security,password,keamanan',
                'view_count'     => 0,
                'is_published'   => 1,
                'created_by'     => $superadmin->id,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('knowledge_base')->insertBatch($articles);

        echo "✅ Knowledge Base seeded successfully! (5 articles)\n";
    }
}
