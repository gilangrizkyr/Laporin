<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Notification extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at'];
    
    protected $casts = [
        'id'           => 'integer',
        'user_id'      => 'integer',
        'complaint_id' => 'integer',
        'is_read'      => 'boolean',
    ];

    public function isRead(): bool
    {
        return (bool) $this->attributes['is_read'];
    }

    public function markAsRead(): self
    {
        $this->attributes['is_read'] = true;
        return $this;
    }

    public function getTimeDiff(): string
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
