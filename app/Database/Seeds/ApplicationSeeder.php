<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run()
    {
        $applications = [
            [
                'name'        => 'SIMPEG (Sistem Informasi Kepegawaian)',
                'description' => 'Aplikasi untuk mengelola data kepegawaian, absensi, dan cuti pegawai',
                'is_critical' => 1, // Aplikasi kritikal
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'E-Office (Surat Menyurat)',
                'description' => 'Aplikasi untuk pengelolaan surat masuk, surat keluar, dan disposisi',
                'is_critical' => 1, // Aplikasi kritikal
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'SIMKEU (Sistem Informasi Keuangan)',
                'description' => 'Aplikasi pengelolaan keuangan, anggaran, dan pelaporan',
                'is_critical' => 1, // Aplikasi kritikal
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'SIPBJ (Sistem Informasi Pengadaan Barang/Jasa)',
                'description' => 'Aplikasi untuk pengelolaan pengadaan dan inventaris barang',
                'is_critical' => 0,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'E-Presensi',
                'description' => 'Aplikasi absensi online pegawai',
                'is_critical' => 0,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Website Instansi',
                'description' => 'Website resmi instansi untuk informasi publik',
                'is_critical' => 0,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Portal Pegawai',
                'description' => 'Portal self-service untuk pegawai',
                'is_critical' => 0,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'SIMPEG Mobile',
                'description' => 'Aplikasi mobile SIMPEG untuk Android/iOS',
                'is_critical' => 0,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('applications')->insertBatch($applications);
        
        echo "✅ Applications seeded successfully! (8 applications)\n";
    }
}