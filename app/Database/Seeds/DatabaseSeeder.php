<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Jalankan semua seeder secara berurutan
        $this->call('UserSeeder');
        $this->call('ApplicationSeeder');
        $this->call('CategorySeeder');
        $this->call('KnowledgeBaseSeeder');

        echo "\n✅ All seeders completed successfully!\n";
    }
}
