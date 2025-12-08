<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'type', 'title', 'message', 'related_id', 'related_type', 'is_read', 'read_at'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Get unread notification count for a user
    public function getUnreadCount(int $userId): int
    {
        return $this->where(['user_id' => $userId, 'is_read' => 0])->countAllResults();
    }

    // Get recent notifications for a user
    public function getRecentNotifications(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    // Get notifications by user (unread only option)
    public function getNotificationsByUser(int $userId, ?bool $unreadOnly = false): array
    {
        $builder = $this->where('user_id', $userId);
        
        if ($unreadOnly) {
            $builder->where('is_read', 0);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    // Count unread for a user
    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    // Mark notification as read
    public function markAsRead(int $id): bool
    {
        return $this->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Mark all notifications as read for user
    public function markAllAsRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                    ->update();
    }

    // Create notification
    public function createNotification(array $data): int
    {
        return $this->insert($data, true);
    }

    // Delete old read notifications (older than 30 days)
    public function cleanupOldNotifications(int $days = 30): bool
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('is_read', 1)
                    ->where('created_at <', $cutoffDate)
                    ->delete();
    }
}
