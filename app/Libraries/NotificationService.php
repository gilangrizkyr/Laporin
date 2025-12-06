<?php

namespace App\Libraries;

use App\Models\NotificationModel;
use App\Models\UserModel;

class NotificationService
{
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
    }

    /**
     * Send notification to user
     */
    public function send(int $userId, string $title, string $message, ?int $complaintId = null): bool
    {
        return $this->notificationModel->insert([
            'user_id'      => $userId,
            'complaint_id' => $complaintId,
            'title'        => $title,
            'message'      => $message,
            'is_read'      => 0,
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultiple(array $userIds, string $title, string $message, ?int $complaintId = null): bool
    {
        $data = [];
        foreach ($userIds as $userId) {
            $data[] = [
                'user_id'      => $userId,
                'complaint_id' => $complaintId,
                'title'        => $title,
                'message'      => $message,
                'is_read'      => 0,
            ];
        }

        return $this->notificationModel->insertBatch($data);
    }

    /**
     * Notify all admins
     */
    public function notifyAdmins(string $title, string $message, ?int $complaintId = null): bool
    {
        $admins = $this->userModel->getAllAdmins();
        $adminIds = array_column($admins, 'id');
        
        return $this->sendToMultiple($adminIds, $title, $message, $complaintId);
    }

    /**
     * Notify when new complaint created
     */
    public function notifyNewComplaint(int $complaintId, string $complaintTitle): bool
    {
        return $this->notifyAdmins(
            'Laporan Baru',
            "Laporan baru telah dibuat: {$complaintTitle}",
            $complaintId
        );
    }

    /**
     * Notify user when complaint status changed
     */
    public function notifyStatusChange(int $userId, int $complaintId, string $oldStatus, string $newStatus): bool
    {
        $statusLabels = [
            'pending'     => 'Pending',
            'in_progress' => 'Sedang Diproses',
            'resolved'    => 'Selesai',
            'closed'      => 'Ditutup',
        ];

        return $this->send(
            $userId,
            'Status Laporan Berubah',
            "Status laporan Anda berubah dari {$statusLabels[$oldStatus]} menjadi {$statusLabels[$newStatus]}",
            $complaintId
        );
    }

    /**
     * Notify user when complaint assigned
     */
    public function notifyAssigned(int $userId, int $complaintId, string $adminName): bool
    {
        return $this->send(
            $userId,
            'Laporan Ditangani',
            "Laporan Anda sedang ditangani oleh {$adminName}",
            $complaintId
        );
    }

    /**
     * Notify admin when assigned to complaint
     */
    public function notifyAdminAssignment(int $adminId, int $complaintId, string $complaintTitle): bool
    {
        return $this->send(
            $adminId,
            'Laporan Baru Ditugaskan',
            "Anda ditugaskan untuk menangani: {$complaintTitle}",
            $complaintId
        );
    }

    /**
     * Notify user when new chat message
     */
    public function notifyNewChatMessage(int $userId, int $complaintId, string $senderName): bool
    {
        return $this->send(
            $userId,
            'Pesan Baru',
            "{$senderName} mengirim pesan baru pada laporan Anda",
            $complaintId
        );
    }

    /**
     * Notify user when complaint resolved
     */
    public function notifyResolved(int $userId, int $complaintId): bool
    {
        return $this->send(
            $userId,
            'Laporan Selesai',
            'Laporan Anda telah diselesaikan. Silakan berikan feedback.',
            $complaintId
        );
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationModel->countUnread($userId);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        return $this->notificationModel->markAsRead($notificationId);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->notificationModel->markAllAsRead($userId);
    }
}