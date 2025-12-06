<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\KnowledgeBase;

class KnowledgeBaseModel extends Model
{
    protected $table            = 'knowledge_base';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = KnowledgeBase::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'content',
        'application_id',
        'category_id',
        'tags',
        'view_count',
        'is_published',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPublishedArticles(?int $limit = null): array
    {
        $builder = $this->where('is_published', 1)
                        ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    public function searchArticles(string $keyword): array
    {
        return $this->like('title', $keyword)
                    ->orLike('content', $keyword)
                    ->orLike('tags', $keyword)
                    ->where('is_published', 1)
                    ->findAll();
    }

    public function incrementViewCount(int $id): bool
    {
        return $this->set('view_count', 'view_count+1', false)
                    ->where('id', $id)
                    ->update();
    }

    public function getPopularArticles(int $limit = 5): array
    {
        return $this->where('is_published', 1)
                    ->orderBy('view_count', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}