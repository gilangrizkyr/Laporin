<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Complaint extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at', 'updated_at', 'resolved_at', 'closed_at'];
    
    protected $casts = [
        'id'             => 'integer',
        'user_id'        => 'integer',
        'application_id' => 'integer',
        'category_id'    => 'integer',
        'assigned_to'    => 'integer',
    ];

    // ========== STATUS CHECKER METHODS ==========
    
    public function isPending(): bool
    {
        return $this->attributes['status'] === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->attributes['status'] === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->attributes['status'] === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->attributes['status'] === 'closed';
    }

    // ========== PRIORITY CHECKER METHODS ==========
    
    public function isNormal(): bool
    {
        return $this->attributes['priority'] === 'normal';
    }

    public function isImportant(): bool
    {
        return $this->attributes['priority'] === 'important';
    }

    public function isUrgent(): bool
    {
        return $this->attributes['priority'] === 'urgent';
    }

    // ========== PERMISSION METHODS ==========
    
    public function canBeEditedBy(int $userId, string $role): bool
    {
        // Superadmin bisa edit semua
        if ($role === 'superadmin') {
            return true;
        }

        // Admin bisa edit semua
        if ($role === 'admin') {
            return true;
        }

        // User hanya bisa edit laporan sendiri yang masih pending
        return $this->attributes['user_id'] === $userId && $this->isPending();
    }

    public function canBeDeletedBy(int $userId, string $role): bool
    {
        // Hanya superadmin yang bisa delete
        if ($role === 'superadmin') {
            return true;
        }

        // User bisa delete laporan sendiri yang masih pending
        return $this->attributes['user_id'] === $userId && $this->isPending();
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->attributes['user_id'] === $userId;
    }

    public function isAssignedTo(int $adminId): bool
    {
        return $this->attributes['assigned_to'] === $adminId;
    }

    // ========== SETTER METHODS ==========
    
    public function setStatus(string $status): self
    {
        $validStatuses = ['pending', 'in_progress', 'resolved', 'closed'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        $this->attributes['status'] = $status;
        return $this;
    }

    public function setPriority(string $priority): self
    {
        $validPriorities = ['normal', 'important', 'urgent'];
        
        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException("Invalid priority: {$priority}");
        }

        $this->attributes['priority'] = $priority;
        return $this;
    }

    // ========== BUSINESS LOGIC METHODS ==========
    
    public function assignTo(int $adminId): self
    {
        $this->attributes['assigned_to'] = $adminId;
        $this->setStatus('in_progress');
        return $this;
    }

    public function resolve(): self
    {
        $this->setStatus('resolved');
        $this->attributes['resolved_at'] = date('Y-m-d H:i:s');
        return $this;
    }

    public function close(): self
    {
        $this->setStatus('closed');
        $this->attributes['closed_at'] = date('Y-m-d H:i:s');
        return $this;
    }

    public function reopen(): self
    {
        $this->setStatus('in_progress');
        $this->attributes['resolved_at'] = null;
        $this->attributes['closed_at'] = null;
        return $this;
    }

    // ========== UI HELPER METHODS ==========
    
    public function getStatusBadge(): string
    {
        $badges = [
            'pending'     => '<span class="badge bg-warning text-dark">Pending</span>',
            'in_progress' => '<span class="badge bg-info">In Progress</span>',
            'resolved'    => '<span class="badge bg-success">Resolved</span>',
            'closed'      => '<span class="badge bg-secondary">Closed</span>',
        ];

        return $badges[$this->attributes['status']] ?? '';
    }

    public function getPriorityBadge(): string
    {
        $badges = [
            'normal'    => '<span class="badge bg-secondary">Normal</span>',
            'important' => '<span class="badge bg-warning text-dark">Important</span>',
            'urgent'    => '<span class="badge bg-danger">Urgent</span>',
        ];

        return $badges[$this->attributes['priority']] ?? '';
    }

    public function getImpactLabel(): string
    {
        $labels = [
            'cannot_use'        => 'Tidak Bisa Digunakan',
            'specific_bug'      => 'Bug Tertentu',
            'slow_performance'  => 'Kinerja Lambat',
            'other'             => 'Lainnya',
        ];

        return $labels[$this->attributes['impact_type']] ?? 'Unknown';
    }

    public function getCreatedDiff(): string
    {
        $created = new \DateTime($this->attributes['created_at']);
        $now = new \DateTime();
        $diff = $now->diff($created);

        if ($diff->days > 0) {
            return $diff->days . ' hari yang lalu';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam yang lalu';
        } elseif ($diff->i > 0) {
            return $diff->i . ' menit yang lalu';
        } else {
            return 'Baru saja';
        }
    }
}