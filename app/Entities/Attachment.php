<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Attachment extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at'];
    
    protected $casts = [
        'id'           => 'integer',
        'complaint_id' => 'integer',
        'file_size'    => 'integer',
        'uploaded_by'  => 'integer',
    ];

    public function getFileUrl(): string
    {
        return base_url($this->attributes['file_path']);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->attributes['file_size'];
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function isImage(): bool
    {
        $imageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        return in_array($this->attributes['file_type'], $imageTypes);
    }

    public function isVideo(): bool
    {
        $videoTypes = ['video/mp4', 'video/avi', 'video/mpeg'];
        return in_array($this->attributes['file_type'], $videoTypes);
    }

    public function getIcon(): string
    {
        if ($this->isImage()) {
            return '<i class="fas fa-image"></i>';
        } elseif ($this->isVideo()) {
            return '<i class="fas fa-video"></i>';
        } else {
            return '<i class="fas fa-file"></i>';
        }
    }
}