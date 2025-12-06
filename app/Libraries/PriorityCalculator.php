<?php

namespace App\Libraries;

use App\Models\ApplicationModel;

class PriorityCalculator
{
    protected $applicationModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
    }

    /**
     * Calculate priority berdasarkan impact_type dan kritikalitas aplikasi
     * 
     * @param string $impactType (cannot_use, specific_bug, slow_performance, other)
     * @param int $applicationId
     * @return string (normal, important, urgent)
     */
    public function calculate(string $impactType, int $applicationId): string
    {
        // Get application data
        $application = $this->applicationModel->find($applicationId);
        
        if (!$application) {
            return 'normal';
        }

        $isCritical = $application->isCritical();

        // Logic prioritas berdasarkan kombinasi impact dan kritikalitas
        switch ($impactType) {
            case 'cannot_use':
                // Aplikasi tidak bisa digunakan
                if ($isCritical) {
                    return 'urgent'; // Aplikasi kritikal + tidak bisa digunakan = URGENT
                }
                return 'important'; // Aplikasi biasa + tidak bisa digunakan = IMPORTANT
                
            case 'specific_bug':
                // Bug tertentu yang mengganggu
                if ($isCritical) {
                    return 'important'; // Aplikasi kritikal + bug = IMPORTANT
                }
                return 'normal'; // Aplikasi biasa + bug = NORMAL
                
            case 'slow_performance':
                // Performa lambat
                if ($isCritical) {
                    return 'important'; // Aplikasi kritikal + lambat = IMPORTANT
                }
                return 'normal'; // Aplikasi biasa + lambat = NORMAL
                
            case 'other':
            default:
                // Lainnya
                return 'normal';
        }
    }

    /**
     * Get priority label in Indonesian
     */
    public function getPriorityLabel(string $priority): string
    {
        $labels = [
            'normal'    => 'Normal',
            'important' => 'Penting',
            'urgent'    => 'Urgent',
        ];

        return $labels[$priority] ?? 'Unknown';
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColor(string $priority): string
    {
        $colors = [
            'normal'    => 'secondary',
            'important' => 'warning',
            'urgent'    => 'danger',
        ];

        return $colors[$priority] ?? 'secondary';
    }

    /**
     * Validate if priority can be manually changed
     * Admin/Superadmin dapat override prioritas
     */
    public function canOverridePriority(string $userRole): bool
    {
        return in_array($userRole, ['admin', 'superadmin']);
    }

    /**
     * Get recommended priority based on multiple factors
     * (Future enhancement: bisa ditambah faktor jumlah user terdampak, dll)
     */
    public function getRecommendedPriority(array $factors): string
    {
        $impactType = $factors['impact_type'] ?? 'other';
        $applicationId = $factors['application_id'] ?? 0;
        
        // Future: tambah faktor lain seperti:
        // - $affectedUsersCount = $factors['affected_users_count'] ?? 0;
        // - $businessImpact = $factors['business_impact'] ?? 'low';
        
        return $this->calculate($impactType, $applicationId);
    }
}