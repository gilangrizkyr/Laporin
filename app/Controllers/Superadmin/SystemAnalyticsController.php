<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SystemAnalyticsController extends BaseController
{
    protected $complaintModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
    }

    public function index()
    {
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');
        $byMonth = $this->complaintModel->getComplaintsByMonth($year);
        $topApps = $this->complaintModel->getTopProblematicApps(20);
        $avgResolution = $this->complaintModel->getAverageResolutionTime();

        return view('superadmin/system_analytics', [
            'title' => 'System Analytics',
            'page_title' => 'System Analytics',
            'year' => $year,
            'byMonth' => $byMonth,
            'topApps' => $topApps,
            'avgResolution' => $avgResolution,
        ]);
    }

    public function export()
    {
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');

        // Get data
        $byMonth = $this->complaintModel->getComplaintsByMonth($year);
        $topApps = $this->complaintModel->getTopProblematicApps(20);
        $avgResolution = $this->complaintModel->getAverageResolutionTime();

        // Build months array
        $months = array_fill(1, 12, 0);
        foreach ($byMonth as $row) {
            $months[$row['month']] = (int) $row['total'];
        }

        // Create CSV
        $output = "System Analytics Report - " . $year . "\n\n";
        $output .= "Monthly Complaints\n";
        $output .= "Month,Total\n";
        for ($m = 1; $m <= 12; $m++) {
            $output .= date('F', mktime(0, 0, 0, $m, 1, $year)) . "," . $months[$m] . "\n";
        }

        $output .= "\nSystem Metrics\n";
        $output .= "Average Resolution Time (hours)," . round($avgResolution, 2) . "\n";

        $output .= "\nTop Applications\n";
        $output .= "Application,Total Complaints\n";
        foreach ($topApps as $app) {
            $output .= esc($app['name']) . "," . $app['total_complaints'] . "\n";
        }

        // Send as download
        $filename = 'system_analytics_' . $year . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $output;
        exit;
    }

    public function exportExcel()
    {
        $year = (int) $this->request->getGet('year') ?: (int) date('Y');

        // Get data
        $byMonth = $this->complaintModel->getComplaintsByMonth($year);
        $topApps = $this->complaintModel->getTopProblematicApps(20);
        $avgResolution = $this->complaintModel->getAverageResolutionTime();

        // Build months array
        $months = array_fill(1, 12, 0);
        foreach ($byMonth as $row) {
            $months[$row['month']] = (int) $row['total'];
        }

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('System Analytics ' . $year);

        // Header
        $sheet->setCellValue('A1', 'System Analytics Report - ' . $year);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Monthly data
        $sheet->setCellValue('A3', 'Monthly Complaints');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->setCellValue('A4', 'Month')->setCellValue('B4', 'Total');
        $row = 5;
        for ($m = 1; $m <= 12; $m++) {
            $sheet->setCellValue('A' . $row, date('F', mktime(0, 0, 0, $m, 1, $year)));
            $sheet->setCellValue('B' . $row, $months[$m]);
            $row++;
        }

        // Average resolution time
        $row += 1;
        $sheet->setCellValue('A' . $row, 'System Metrics');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Average Resolution Time (hours)');
        $sheet->setCellValue('B' . $row, round($avgResolution, 2));
        $row++;

        // Top applications
        $row += 1;
        $sheet->setCellValue('A' . $row, 'Top Applications');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Application')->setCellValue('B' . $row, 'Total Complaints');
        $row++;
        foreach ($topApps as $app) {
            $sheet->setCellValue('A' . $row, $app['name']);
            $sheet->setCellValue('B' . $row, $app['total_complaints']);
            $row++;
        }

        // Auto-fit columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        // Write to output
        $writer = new Xlsx($spreadsheet);
        $filename = 'system_analytics_' . $year . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
