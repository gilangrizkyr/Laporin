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
  /** sini */
    public function getUserComplaintStats(int $userId): array
    {
        $builder = $this->where('user_id', $userId);

        return [
            'total'       => (clone $builder)->countAllResults(),
            'pending'     => (clone $builder)->where('status', 'pending')->countAllResults(),
            'in_progress' => (clone $builder)->where('status', 'in_progress')->countAllResults(),
            'resolved'    => (clone $builder)->where('status', 'resolved')->countAllResults(),
            'closed'      => (clone $builder)->where('status', 'closed')->countAllResults(),
        ];
    }

    /** Ambil recent complaints + nama aplikasi (untuk homepage) */
    public function getRecentComplaints(int $limit = 5): array
    {
        return $this->select('complaints.*, applications.name as application_name')
                    ->join('applications', 'applications.id = complaints.application_id')
                    ->orderBy('complaints.created_at', 'DESC')
                    ->findAll($limit);
    }

    public function getComplaintsByUser(int $userId, ?string $status = null): array
{
    $builder = $this->where('user_id', $userId);
    
    if ($status !== null) {
        $builder->where('status', $status);
    }
    
    return $builder->orderBy('created_at', 'DESC')->findAll();
}
 /** sini */
    // METHOD BARU: INI YANG BIKIN REPORT CANTIK!
    public function getFilteredComplaintsWithRelations(?array $filters = null): array
    {
        $builder = $this->builder();

        $builder->select('
            complaints.*,
            u.full_name as user_full_name,
            u.email as user_email,
            a.name as application_name,
            c.name as category_name
        ');
        $builder->join('users u', 'u.id = complaints.user_id', 'left');
        $builder->join('applications a', 'a.id = complaints.application_id', 'left');
        $builder->join('categories c', 'c.id = complaints.category_id', 'left');

        if ($filters) {
            if (!empty($filters['status']))          $builder->where('complaints.status', $filters['status']);
            if (!empty($filters['priority']))        $builder->where('complaints.priority', $filters['priority']);
            if (!empty($filters['application_id'])) $builder->where('complaints.application_id', $filters['application_id']);
            if (!empty($filters['category_id']))     $builder->where('complaints.category_id', $filters['category_id']);
            if (!empty($filters['assigned_to']))     $builder->where('complaints.assigned_to', $filters['assigned_to']);
            if (!empty($filters['date_from']))       $builder->where('complaints.created_at >=', $filters['date_from']);
            if (!empty($filters['date_to']))         $builder->where('complaints.created_at <=', $filters['date_to']);
        }

        return $builder->orderBy('complaints.created_at', 'DESC')->get()->getResult();
    }

    // Tetap pertahankan method lama biar tidak rusak fitur lain
    public function getAllComplaints(?array $filters = null): array
    {
        $builder = $this;
        if ($filters) {
            if (!empty($filters['status']))          $builder->where('status', $filters['status']);
            if (!empty($filters['priority']))        $builder->where('priority', $filters['priority']);
            if (!empty($filters['application_id'])) $builder->where('application_id', $filters['application_id']);
            if (!empty($filters['assigned_to']))     $builder->where('assigned_to', $filters['assigned_to']);
            if (!empty($filters['date_from']))       $builder->where('created_at >=', $filters['date_from']);
            if (!empty($filters['date_to']))         $builder->where('created_at <=', $filters['date_to']);
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
