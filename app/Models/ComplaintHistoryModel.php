<?php

namespace App\Models;

use CodeIgniter\Model;

class ComplaintHistoryModel extends Model
{
    protected $table = 'complaint_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'complaint_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'description',
        'user_name',
        'user_email'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Get all history for a complaint with user details
    public function getHistoryByComplaint(int $complaintId): array
    {
        return $this->where('complaint_id', $complaintId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Get history with pagination
    public function getHistoryPaginated(int $complaintId, int $page = 1, int $perPage = 20): array
    {
        return $this->where('complaint_id', $complaintId)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);
    }

    // Filter history by action type
    public function getHistoryByAction(int $complaintId, string $action): array
    {
        return $this->where('complaint_id', $complaintId)
            ->where('action', $action)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Get timeline for complaint (recent first)
    public function getTimeline(int $complaintId, int $limit = 50): array
    {
        return $this->where('complaint_id', $complaintId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // Get summary of actions from history array
    public static function getActionSummary(array $history): array
    {
        $summary = [];
        foreach ($history as $h) {
            $action = $h['action'] ?? null;
            if ($action) {
                $summary[$action] = ($summary[$action] ?? 0) + 1;
            }
        }
        return $summary;
    }

    // Log action with user info
    public function logAction(int $complaintId, int $userId, string $action, ?string $oldValue = null, ?string $newValue = null, ?string $description = null, ?string $userName = null, ?string $userEmail = null): int
    {
        return $this->insert([
            'complaint_id' => $complaintId,
            'user_id' => $userId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'user_name' => $userName,
            'user_email' => $userEmail,
        ], true);
    }

    // Get action color/badge for UI
    public static function getActionBadgeClass(string $action): string
    {
        $action = self::normalizeAction($action);
        $badges = [
            'created' => 'success',
            'status_change' => 'info',
            'priority_change' => 'warning',
            'assigned' => 'primary',
            'comment' => 'secondary',
            'resolved' => 'success',
            'closed' => 'dark',
            'reopened' => 'danger',
            'attachment' => 'light',
        ];
        return $badges[$action] ?? 'secondary';
    }

    // Get action icon for UI
    public static function getActionIcon(string $action): string
    {
        $action = self::normalizeAction($action);
        $icons = [
            'created' => 'fa-plus-circle',
            'status_change' => 'fa-exchange-alt',
            'priority_change' => 'fa-arrow-up',
            'assigned' => 'fa-user-check',
            'comment' => 'fa-comment',
            'resolved' => 'fa-check-circle',
            'closed' => 'fa-times-circle',
            'reopened' => 'fa-redo-alt',
            'attachment' => 'fa-paperclip',
        ];
        return $icons[$action] ?? 'fa-circle';
    }

    // Get human-readable action label
    public static function getActionLabel(string $action): string
    {
        $action = self::normalizeAction($action);
        $labels = [
            'created' => 'Laporan Dibuat',
            'status_change' => 'Status Berubah',
            'priority_change' => 'Prioritas Berubah',
            'assigned' => 'Ditugaskan',
            'comment' => 'Komentar Ditambahkan',
            'resolved' => 'Terselesaikan',
            'closed' => 'Ditutup',
            'reopened' => 'Dibuka Kembali',
            'attachment' => 'File Ditambahkan',
        ];
        return $labels[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }

    // Normalize action keys and accept common synonyms
    public static function normalizeAction(?string $action): string
    {
        if (empty($action)) {
            return 'unknown';
        }

        $map = [
            'status_changed' => 'status_change',
            'status_change' => 'status_change',
            'priority_changed' => 'priority_change',
            'priority_change' => 'priority_change',
            'created' => 'created',
            'assigned' => 'assigned',
            'comment' => 'comment',
            'resolved' => 'resolved',
            'closed' => 'closed',
            'reopened' => 'reopened',
            'attachment' => 'attachment',
        ];

        $a = strtolower($action);
        return $map[$a] ?? $a;
    }
}
