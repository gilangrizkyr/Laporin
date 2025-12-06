<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'id'        => 'integer',
        'is_active' => 'boolean',
    ];

    // Jangan return password saat toArray()
    protected $hidden = ['password'];

    // ========== GETTER METHODS ==========
    
    public function isUser(): bool
    {
        return $this->attributes['role'] === 'user';
    }

    public function isAdmin(): bool
    {
        return $this->attributes['role'] === 'admin';
    }

    public function isSuperadmin(): bool
    {
        return $this->attributes['role'] === 'superadmin';
    }

    public function isActive(): bool
    {
        return (bool) $this->attributes['is_active'];
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperadmin();
    }

    public function canHandleComplaints(): bool
    {
        return $this->isAdmin() || $this->isSuperadmin();
    }

    public function canOverridePriority(): bool
    {
        return $this->isAdmin() || $this->isSuperadmin();
    }

    // ========== SETTER METHODS ==========
    
    public function setPassword(string $password): self
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function setRole(string $role): self
    {
        $validRoles = ['user', 'admin', 'superadmin'];
        
        if (!in_array($role, $validRoles)) {
            throw new \InvalidArgumentException("Invalid role: {$role}");
        }

        $this->attributes['role'] = $role;
        return $this;
    }

    // ========== HELPER METHODS ==========
    
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password']);
    }

    public function getRoleBadge(): string
    {
        $badges = [
            'user'       => '<span class="badge bg-primary">User</span>',
            'admin'      => '<span class="badge bg-info">Admin</span>',
            'superadmin' => '<span class="badge bg-danger">Superadmin</span>',
        ];

        return $badges[$this->attributes['role']] ?? '';
    }

    public function getInitials(): string
    {
        $words = explode(' ', $this->attributes['full_name']);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }
}
