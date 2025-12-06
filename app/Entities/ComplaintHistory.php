<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ComplaintHistory extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at'];
    
    protected $casts = [
        'id'           => 'integer',
        'complaint_id' => 'integer',
        'user_id'      => 'integer',
    ];

    public function getActionLabel(): string
    {
        $labels = [
            'created'          => 'Laporan dibuat',
            'status_changed'   => 'Status diubah',
            'priority_changed' => 'Prioritas diubah',
            'assigned'         => 'Laporan di-assign',
            'resolved'         => 'Laporan diselesaikan',
            'closed'           => 'Laporan ditutup',
            'reopened'         => 'Laporan dibuka kembali',
        ];

        return $labels[$this->attributes['action']] ?? $this->attributes['action'];
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