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

   public function delete($id = null, bool $purge = false)
{
    log_message('info', "Attempting to delete user with ID: $id");

    // Pastikan ID ada dan valid
    if ($id === null) {
        log_message('error', "User ID is null.");
        return false;
    }

    // Pastikan user ada di database
    $user = $this->find($id);
    if (!$user) {
        log_message('error', "User with ID $id not found.");
        return false;
    }

    // Melakukan penghapusan
    $result = parent::delete($id, $purge);  // Menyesuaikan dengan parent class method
    if ($result) {
        log_message('info', "User with ID $id deleted successfully.");
    } else {
        log_message('error', "Failed to delete user with ID: $id.");
    }

    return $result;
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
