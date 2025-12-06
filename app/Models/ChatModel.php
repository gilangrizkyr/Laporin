<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Chat;

class ChatModel extends Model
{
    protected $table            = 'chats';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Chat::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'complaint_id',
        'user_id',
        'message',
        'is_internal_note'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'complaint_id' => 'required|integer',
        'user_id'      => 'required|integer',
        'message'      => 'required|min_length[1]',
    ];

    public function getChatsByComplaint(int $complaintId, bool $includeInternal = false): array
    {
        $builder = $this->where('complaint_id', $complaintId);
        
        if (!$includeInternal) {
            $builder->where('is_internal_note', 0);
        }
        
        return $builder->orderBy('created_at', 'ASC')->findAll();
    }

    public function getLatestChat(int $complaintId): ?Chat
    {
        return $this->where('complaint_id', $complaintId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    public function countUnreadChats(int $complaintId, int $userId): int
    {
        // This is simplified - you may want to add a 'read_by' tracking table
        return 0;
    }
}
