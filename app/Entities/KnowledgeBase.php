<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class KnowledgeBase extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'id'             => 'integer',
        'application_id' => 'integer',
        'category_id'    => 'integer',
        'view_count'     => 'integer',
        'is_published'   => 'boolean',
        'created_by'     => 'integer',
    ];

    public function isPublished(): bool
    {
        return (bool) $this->attributes['is_published'];
    }

    public function incrementView(): void
    {
        $this->attributes['view_count']++;
    }

    public function getTagsArray(): array
    {
        if (empty($this->attributes['tags'])) {
            return [];
        }

        return array_map('trim', explode(',', $this->attributes['tags']));
    }

    public function getExcerpt(int $length = 150): string
    {
        $content = strip_tags($this->attributes['content']);
        
        if (strlen($content) > $length) {
            return substr($content, 0, $length) . '...';
        }

        return $content;
    }
}