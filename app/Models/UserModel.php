<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\User;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'email',
        'password',
        'full_name',
        'role',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'username'  => 'required|min_length[3]|max_length[50]',
        'email'     => 'required|valid_email',
        'password'  => 'permit_empty|min_length[6]',
        'full_name' => 'required|min_length[3]|max_length[100]',
        'role'      => 'required|in_list[user,admin,superadmin]',
    ];


    protected $validationMessages = [
        'username' => [
            'required'   => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'is_unique'  => 'Username sudah digunakan'
        ],
        'email' => [
            'required'    => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique'   => 'Email sudah terdaftar'
        ],
        'password' => [
            'min_length' => 'Password minimal 6 karakter'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        // Hanya hash password jika field diisi
        if (!empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']); // jangan update password jika kosong
        }
        return $data;
    }

    // ========== CUSTOM QUERY METHODS ==========

    public function getUserByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->where('username', $username)->first();
    }

    public function getActiveUsers(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    public function getUsersByRole(string $role): array
    {
        return $this->where('role', $role)
            ->where('is_active', 1)
            ->findAll();
    }

    public function getAllAdmins(): array
    {
        return $this->whereIn('role', ['admin', 'superadmin'])
            ->where('is_active', 1)
            ->findAll();
    }

    public function searchUsers(string $keyword): array
    {
        return $this->like('full_name', $keyword)
            ->orLike('username', $keyword)
            ->orLike('email', $keyword)
            ->findAll();
    }

    public function toggleActive(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;

        return $this->update($userId, [
            'is_active' => !$user->is_active
        ]);
    }
}
