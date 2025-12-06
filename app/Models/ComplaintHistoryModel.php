<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ComplaintHistory;

class ComplaintHistoryModel extends Model
{
    protected $table            = 'complaint_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = ComplaintHistory::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'complaint_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'description'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getHistoryByComplaint(int $complaintId): array
    {
        return $this->where('complaint_id', $complaintId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function logAction(int $complaintId, int $userId, string $action, ?string $oldValue = null, ?string $newValue = null, ?string $description = null): bool
    {
        return $this->insert([
            'complaint_id' => $complaintId,
            'user_id'      => $userId,
            'action'       => $action,
            'old_value'    => $oldValue,
            'new_value'    => $newValue,
            'description'  => $description,
        ]);
    }
}