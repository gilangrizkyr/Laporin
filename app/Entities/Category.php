<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Category extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'id'        => 'integer',
        'is_active' => 'boolean',
    ];

    public function isActive(): bool
    {
        return (bool) $this->attributes['is_active'];
    }
}