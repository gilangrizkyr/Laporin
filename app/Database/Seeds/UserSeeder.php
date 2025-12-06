<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Superadmin Default
        $this->db->table('users')->insert([
            'username'   => 'superadmin',
            'email'      => 'superadmin@example.com',
            'password'   => password_hash('superadmin123', PASSWORD_DEFAULT),
            'full_name'  => 'Super Administrator',
            'role'       => 'superadmin',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Admin Default
        $this->db->table('users')->insert([
            'username'   => 'admin',
            'email'      => 'admin@example.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name'  => 'Administrator',
            'role'       => 'admin',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // User Demo 1
        $this->db->table('users')->insert([
            'username'   => 'user1',
            'email'      => 'user1@example.com',
            'password'   => password_hash('user123', PASSWORD_DEFAULT),
            'full_name'  => 'User Demo 1',
            'role'       => 'user',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // User Demo 2
        $this->db->table('users')->insert([
            'username'   => 'user2',
            'email'      => 'user2@example.com',
            'password'   => password_hash('user123', PASSWORD_DEFAULT),
            'full_name'  => 'User Demo 2',
            'role'       => 'user',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        echo "✅ Users seeded successfully!\n";
        echo "   - Superadmin: superadmin@example.com / superadmin123\n";
        echo "   - Admin: admin@example.com / admin123\n";
        echo "   - User1: user1@example.com / user123\n";
        echo "   - User2: user2@example.com / user123\n";
    }
}