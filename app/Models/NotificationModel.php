<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Notification;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Notification::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'complaint_id',
        'title',
        'message',
        'is_read'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getNotificationsByUser(int $userId, ?bool $unreadOnly = false): array
    {
        $builder = $this->where('user_id', $userId);

        if ($unreadOnly) {
            $builder->where('is_read', 0);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->set('is_read', 1)
            ->update();
    }
}
