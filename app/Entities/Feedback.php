<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Feedback extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at'];
    
    protected $casts = [
        'id'           => 'integer',
        'complaint_id' => 'integer',
        'user_id'      => 'integer',
        'rating'       => 'integer',
    ];

    public function getRatingStars(): string
    {
        $rating = $this->attributes['rating'];
        $stars = '';
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-muted"></i>';
            }
        }
        
        return $stars;
    }

    public function isGood(): bool
    {
        return $this->attributes['rating'] >= 4;
    }

    public function isBad(): bool
    {
        return $this->attributes['rating'] <= 2;
    }
}