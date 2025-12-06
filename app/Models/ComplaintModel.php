<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Complaint;

class ComplaintModel extends Model
{
    protected $table            = 'complaints';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Complaint::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'application_id',
        'category_id',
        'title',
        'description',
        'impact_type',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'        => 'required|integer',
        'application_id' => 'required|integer',
        'title'          => 'required|min_length[5]|max_length[200]',
        'description'    => 'required|min_length[10]',
        'impact_type'    => 'required|in_list[cannot_use,specific_bug,slow_performance,other]',
        'priority'       => 'in_list[normal,important,urgent]',
        'status'         => 'in_list[pending,in_progress,resolved,closed]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Judul laporan harus diisi',
            'min_length' => 'Judul minimal 5 karakter'
        ],
        'description' => [
            'required'   => 'Deskripsi laporan harus diisi',
            'min_length' => 'Deskripsi minimal 10 karakter'
        ]
    ];

    // ========== QUERY METHODS FOR USER ==========

    public function getComplaintsByUser(int $userId, ?string $status = null): array
    {
        $builder = $this->where('user_id', $userId);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getUserComplaintStats(int $userId): array
    {
        return [
            'total'       => $this->where('user_id', $userId)->countAllResults(),
            'pending'     => $this->where(['user_id' => $userId, 'status' => 'pending'])->countAllResults(),
            'in_progress' => $this->where(['user_id' => $userId, 'status' => 'in_progress'])->countAllResults(),
            'resolved'    => $this->where(['user_id' => $userId, 'status' => 'resolved'])->countAllResults(),
            'closed'      => $this->where(['user_id' => $userId, 'status' => 'closed'])->countAllResults(),
        ];
    }

    // ========== QUERY METHODS FOR ADMIN ==========

    public function getAllComplaints(?array $filters = null): array
    {
        $builder = $this;

        if ($filters) {
            if (!empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (!empty($filters['priority'])) {
                $builder->where('priority', $filters['priority']);
            }
            if (!empty($filters['application_id'])) {
                $builder->where('application_id', $filters['application_id']);
            }
            if (!empty($filters['assigned_to'])) {
                $builder->where('assigned_to', $filters['assigned_to']);
            }
            if (!empty($filters['date_from'])) {
                $builder->where('created_at >=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $builder->where('created_at <=', $filters['date_to']);
            }
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getComplaintsByAdmin(int $adminId): array
    {
        return $this->where('assigned_to', $adminId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getUnassignedComplaints(): array
    {
        return $this->where('assigned_to', null)
                    ->where('status', 'pending')
                    ->orderBy('priority', 'DESC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getUrgentComplaints(): array
    {
        return $this->where('priority', 'urgent')
                    ->whereNotIn('status', ['closed'])
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    // ========== STATISTICS METHODS ==========

    public function getGlobalStats(): array
    {
        return [
            'total'       => $this->countAllResults(),
            'pending'     => $this->where('status', 'pending')->countAllResults(),
            'in_progress' => $this->where('status', 'in_progress')->countAllResults(),
            'resolved'    => $this->where('status', 'resolved')->countAllResults(),
            'closed'      => $this->where('status', 'closed')->countAllResults(),
            'urgent'      => $this->where('priority', 'urgent')->whereNotIn('status', ['closed'])->countAllResults(),
        ];
    }

    public function getComplaintsByMonth(int $year): array
    {
        $sql = "SELECT 
                    MONTH(created_at) as month,
                    COUNT(*) as total
                FROM {$this->table}
                WHERE YEAR(created_at) = ?
                GROUP BY MONTH(created_at)
                ORDER BY month";
        
        return $this->db->query($sql, [$year])->getResultArray();
    }

    public function getTopProblematicApps(int $limit = 10): array
    {
        $sql = "SELECT 
                    a.name,
                    COUNT(c.id) as total_complaints
                FROM {$this->table} c
                JOIN applications a ON c.application_id = a.id
                GROUP BY c.application_id
                ORDER BY total_complaints DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit])->getResultArray();
    }

    public function getAverageResolutionTime(): ?float
    {
        $sql = "SELECT 
                    AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours
                FROM {$this->table}
                WHERE status = 'resolved'
                AND resolved_at IS NOT NULL";
        
        $result = $this->db->query($sql)->getRow();
        return $result ? round($result->avg_hours, 2) : null;
    }

    // ========== SEARCH METHOD ==========

    public function searchComplaints(string $keyword, ?int $userId = null): array
    {
        $builder = $this->like('title', $keyword)
                        ->orLike('description', $keyword);

        if ($userId) {
            $builder->where('user_id', $userId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}
