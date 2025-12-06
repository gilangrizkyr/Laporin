<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Application extends Entity
{
    protected $datamap = [];

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'id'          => 'integer',
        'is_critical' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function isCritical(): bool
    {
        return (bool) $this->attributes['is_critical'];
    }

    public function isActive(): bool
    {
        return (bool) $this->attributes['is_active'];
    }

    public function getCriticalBadge(): string
    {
        if ($this->isCritical()) {
            return '<span class="badge bg-danger">Critical</span>';
        }
        return '<span class="badge bg-secondary">Normal</span>';
    }
}
