<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Feedback;

class FeedbackModel extends Model
{
    protected $table            = 'feedbacks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Feedback::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'complaint_id',
        'user_id',
        'rating',
        'comment'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'complaint_id' => 'required|integer',
        'user_id'      => 'required|integer',
        'rating'       => 'required|integer|greater_than[0]|less_than[6]',
    ];

    public function getFeedbackByComplaint(int $complaintId): ?Feedback
    {
        return $this->where('complaint_id', $complaintId)->first();
    }

    public function getAverageRating(): ?float
    {
        $result = $this->selectAvg('rating')->first();
        return $result ? round($result->rating, 2) : null;
    }

    public function getRatingDistribution(): array
    {
        $sql = "SELECT rating, COUNT(*) as count 
                FROM {$this->table} 
                GROUP BY rating 
                ORDER BY rating DESC";
        
        return $this->db->query($sql)->getResultArray();
    }
}