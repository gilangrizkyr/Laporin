<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\ApplicationModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AnalyticsController extends BaseController
{
    protected $complaintModel;
    protected $applicationModel;
    protected $userModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->applicationModel = new ApplicationModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');

        // Complaints per month
        $byMonthRaw = $this->complaintModel->getComplaintsByMonth($year);
        $months = array_fill(1, 12, 0);
        foreach ($byMonthRaw as $r) {
            $m = (int) $r['month'];
            $months[$m] = (int) $r['total'];
        }

        // Average resolution time (hours)
        $avgResolution = $this->complaintModel->getAverageResolutionTime();

        // Complaints by application (all)
        $topApps = $this->complaintModel->getTopProblematicApps(50);

        // Complaints by priority (use direct query to avoid model state issues)
        $priorityRows = $this->complaintModel->db->query("SELECT priority, COUNT(*) as total FROM complaints GROUP BY priority")->getResultArray();
        $priorities = [
            'normal' => 0,
            'important' => 0,
            'urgent' => 0,
        ];
        foreach ($priorityRows as $pr) {
            $key = $pr['priority'];
            if (isset($priorities[$key])) {
                $priorities[$key] = (int) $pr['total'];
            }
        }

        // Admin performance metrics
        $admins = $this->userModel->getAllAdmins();
        $adminPerformance = [];
        foreach ($admins as $admin) {
            $adminId = $admin->id;
            // total assigned
            $totalAssigned = $this->complaintModel->where('assigned_to', $adminId)->countAllResults(false);
            // resolved by this admin (resolved_at not null and status = resolved)
            $resolvedCount = $this->complaintModel->where(['assigned_to' => $adminId, 'status' => 'resolved'])->countAllResults(false);
            // avg resolution hours for this admin
            $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours FROM complaints WHERE assigned_to = ? AND status = 'resolved' AND resolved_at IS NOT NULL";
            $row = $this->complaintModel->db->query($sql, [$adminId])->getRow();
            $avgHours = $row ? round($row->avg_hours, 2) : null;

            $adminPerformance[] = [
                'id' => $adminId,
                'name' => $admin->full_name,
                'total_assigned' => (int) $totalAssigned,
                'resolved' => (int) $resolvedCount,
                'avg_resolution_hours' => $avgHours,
            ];
        }

        $data = [
            'title' => 'Admin Analytics',
            'page_title' => 'Analytics',
            'year' => $year,
            'months' => array_values($months),
            'avgResolution' => $avgResolution,
            'topApps' => $topApps,
            'priorities' => $priorities,
            'adminPerformance' => $adminPerformance,
        ];

        return view('admin/analytics', $data);
    }

    // JSON APIs for charts
    public function apiMonthlyAvgResolution($year = null)
    {
        $year = $year ? (int) $year : ((int) $this->request->getGet('year') ?: (int) date('Y'));
        // Calculate average resolution time (hours) per month
        $sql = "SELECT MONTH(resolved_at) as month, AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours FROM complaints WHERE YEAR(resolved_at) = ? GROUP BY MONTH(resolved_at) ORDER BY month";
        $rows = $this->complaintModel->db->query($sql, [$year])->getResultArray();
        $months = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $m = (int) $r['month'];
            $months[$m] = $r['avg_hours'] !== null ? round($r['avg_hours'], 2) : 0;
        }

        return $this->response->setJSON(['year' => $year, 'data' => array_values($months)]);
    }

    public function apiMonthlyTotals($year = null)
    {
        $year = $year ? (int) $year : ((int) $this->request->getGet('year') ?: (int) date('Y'));
        $rows = $this->complaintModel->getComplaintsByMonth($year);
        $months = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $m = (int) $r['month'];
            $months[$m] = (int) $r['total'];
        }
        return $this->response->setJSON(['year' => $year, 'data' => array_values($months)]);
    }

    public function apiComplaintsByApp($year = null)
    {
        $year = $year ? (int) $year : ((int) $this->request->getGet('year') ?: (int) date('Y'));
        $sql = "SELECT a.name, COUNT(c.id) as total FROM complaints c JOIN applications a ON c.application_id = a.id WHERE YEAR(c.created_at) = ? GROUP BY c.application_id ORDER BY total DESC LIMIT 50";
        $rows = $this->complaintModel->db->query($sql, [$year])->getResultArray();
        return $this->response->setJSON(['year' => $year, 'data' => $rows]);
    }

    public function apiAdminPerformance()
    {
        $admins = $this->userModel->getAllAdmins();
        $data = [];
        foreach ($admins as $admin) {
            $adminId = $admin->id;
            $totalAssigned = (int) $this->complaintModel->where('assigned_to', $adminId)->countAllResults(false);
            $resolvedCount = (int) $this->complaintModel->where(['assigned_to' => $adminId, 'status' => 'resolved'])->countAllResults(false);
            $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours FROM complaints WHERE assigned_to = ? AND status = 'resolved' AND resolved_at IS NOT NULL";
            $row = $this->complaintModel->db->query($sql, [$adminId])->getRow();
            $avgHours = $row ? round($row->avg_hours, 2) : null;
            $data[] = [
                'id' => $adminId,
                'name' => $admin->full_name,
                'total_assigned' => $totalAssigned,
                'resolved' => $resolvedCount,
                'avg_resolution_hours' => $avgHours,
            ];
        }
        return $this->response->setJSON($data);
    }

    public function exportPdf()
    {
        // Simple PDF export using Dompdf
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');
        $byMonthRaw = $this->complaintModel->getComplaintsByMonth($year);
        $months = array_fill(1, 12, 0);
        foreach ($byMonthRaw as $r) {
            $m = (int) $r['month'];
            $months[$m] = (int) $r['total'];
        }
        $topApps = $this->complaintModel->getTopProblematicApps(50);
        $priorities = $this->complaintModel->db->query("SELECT priority, COUNT(*) as total FROM complaints GROUP BY priority")->getResultArray();

        $data = [
            'year' => $year,
            'months' => $months,
            'topApps' => $topApps,
            'priorities' => $priorities,
        ];

        $html = view('admin/analytics_pdf', $data);

        // Load Dompdf
        if (!class_exists('\Dompdf\Dompdf')) {
            return $this->response->setBody('Dompdf library not found. Install dompdf/dompdf via Composer.');
        }

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "attachment; filename=analytics_{$year}.pdf")
            ->setBody($output);
    }

    public function export()
    {
        // Export a simple CSV summarizing analytics for current year
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');

        $byMonthRaw = $this->complaintModel->getComplaintsByMonth($year);
        $months = array_fill(1, 12, 0);
        foreach ($byMonthRaw as $r) {
            $m = (int) $r['month'];
            $months[$m] = (int) $r['total'];
        }

        $topApps = $this->complaintModel->getTopProblematicApps(50);

        $csvLines = [];
        $csvLines[] = ["Analytics Export - Year: {$year}"];
        $csvLines[] = ['Month', 'Complaints'];
        foreach ($months as $num => $count) {
            $csvLines[] = [date('F', mktime(0, 0, 0, $num, 1, $year)), $count];
        }

        $csvLines[] = [];
        $csvLines[] = ['Top Problematic Applications'];
        $csvLines[] = ['Application', 'Total Complaints'];
        foreach ($topApps as $app) {
            $csvLines[] = [$app['name'], $app['total_complaints']];
        }

        // Build CSV
        $out = fopen('php://memory', 'w');
        foreach ($csvLines as $line) {
            fputcsv($out, $line);
        }
        rewind($out);
        $csv = stream_get_contents($out);

        return $this->response->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', "attachment; filename=analytics_{$year}.csv")
            ->setBody($csv);
    }

    public function exportExcel()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            return $this->response->setBody('PhpSpreadsheet not found. Run: composer require phpoffice/phpspreadsheet');
        }

        $year = (int) $this->request->getGet('year') ?: (int) date('Y');
        $rows = $this->complaintModel->getComplaintsByMonth($year);
        $months = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $months[(int)$r['month']] = (int)$r['total'];
        }

        $topApps = $this->complaintModel->getTopProblematicApps(50);
        $priorityRows = $this->complaintModel->db->query("SELECT priority, COUNT(*) as total FROM complaints GROUP BY priority")->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analytics ' . $year);

        // Months section
        $sheet->setCellValue('A1', 'Month')->setCellValue('B1', 'Total');
        $row = 2;
        for ($m = 1; $m <= 12; $m++) {
            $sheet->setCellValue('A' . $row, date('F', mktime(0, 0, 0, $m, 1, $year)));
            $sheet->setCellValue('B' . $row, $months[$m]);
            $row++;
        }

        // Top Applications
        $row += 1;
        $sheet->setCellValue('A' . $row, 'Top Applications');
        $row++;
        $sheet->setCellValue('A' . $row, 'Application')->setCellValue('B' . $row, 'Total');
        $row++;
        foreach ($topApps as $app) {
            $sheet->setCellValue('A' . $row, $app['name']);
            $sheet->setCellValue('B' . $row, $app['total_complaints']);
            $row++;
        }

        // Priorities
        $row += 1;
        $sheet->setCellValue('A' . $row, 'Priority')->setCellValue('B' . $row, 'Total');
        $row++;
        foreach ($priorityRows as $p) {
            $sheet->setCellValue('A' . $row, ucfirst($p['priority']));
            $sheet->setCellValue('B' . $row, (int)$p['total']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "analytics_{$year}.xlsx";
        return $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', "attachment; filename={$fileName}")
            ->setBody($writer->save('php://output'));
    }

    /**
     * Export analytics data and charts as PDF
     */
    public function exportAnalyticsPdf()
    {
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');

        // Get all analytics data
        $byMonthRaw = $this->complaintModel->getComplaintsByMonth($year);
        $months = array_fill(1, 12, 0);
        foreach ($byMonthRaw as $r) {
            $m = (int) $r['month'];
            $months[$m] = (int) $r['total'];
        }

        $avgResolution = $this->complaintModel->getAverageResolutionTime();
        $topApps = $this->complaintModel->getTopProblematicApps(10);
        $stats = $this->complaintModel->getGlobalStats();

        $priorityRows = $this->complaintModel->db->query("SELECT priority, COUNT(*) as total FROM complaints GROUP BY priority")->getResultArray();
        $priorities = [
            'normal' => 0,
            'important' => 0,
            'urgent' => 0,
        ];
        foreach ($priorityRows as $p) {
            $priorities[$p['priority']] = (int)$p['total'];
        }

        // Generate HTML
        $html = view('admin/analytics/export_pdf', [
            'year' => $year,
            'months' => $months,
            'avgResolution' => $avgResolution,
            'topApps' => $topApps,
            'stats' => $stats,
            'priorities' => $priorities,
        ]);

        // Generate PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Analytics_' . $year . '_' . date('Y-m-d') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
}
