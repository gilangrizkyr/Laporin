<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\ApplicationModel;
use App\Models\CategoryModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportGeneratorController extends BaseController
{
    protected $complaintModel;
    protected $applicationModel;
    protected $categoryModel;
    protected $userModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->applicationModel = new ApplicationModel();
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
    }

    /**
     * Show custom report builder form
     */
    public function index()
    {
        $applications = $this->applicationModel->findAll();
        $categories = $this->categoryModel->findAll();
        $admins = $this->userModel->where('role', 'admin')->findAll();

        return view('admin/reports/builder', [
            'title' => 'Custom Report Generator',
            'applications' => $applications,
            'categories' => $categories,
            'admins' => $admins,
        ]);
    }

    /**
     * Generate report based on selected criteria
     */
    public function generate()
    {
        $metrics = $this->request->getPost('metrics') ?? [];
        $dateFrom = $this->request->getPost('date_from');
        $dateTo = $this->request->getPost('date_to');
        $appId = $this->request->getPost('app_id');
        $catId = $this->request->getPost('category_id');
        $status = $this->request->getPost('status');
        $priority = $this->request->getPost('priority');
        $format = $this->request->getPost('format') ?? 'pdf'; // pdf or excel
        $assignedTo = $this->request->getPost('assigned_to');

        // Build filters
        $filters = array_filter([
            'date_from' => $dateFrom,
            'date_to' => $dateTo ? $dateTo . ' 23:59:59' : null,
            'application_id' => $appId,
            'category_id' => $catId,
            'status' => $status,
            'priority' => $priority,
            'assigned_to' => $assignedTo,
        ]);

        // Get filtered data
        $complaints = $this->complaintModel->getAllComplaints($filters);

        // Store report in session for download
        session()->set('report_data', [
            'complaints' => $complaints,
            'metrics' => $metrics,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'app_id' => $appId,
                'cat_id' => $catId,
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $assignedTo,
            ],
            'format' => $format,
        ]);

        // Generate preview
        return view('admin/reports/preview', [
            'title' => 'Report Preview',
            'complaints' => $complaints,
            'metrics' => $metrics,
            'filters' => $filters,
            'format' => $format,
        ]);
    }

    /**
     * Download generated report
     */
    public function download(string $format)
    {
        $reportData = session()->get('report_data');
        if (!$reportData) {
            return redirect()->to('admin/reports')->with('error', 'Report data not found');
        }

        $complaints = $reportData['complaints'];
        $metrics = $reportData['metrics'];
        $filters = $reportData['filters'];

        if ($format === 'excel') {
            return $this->generateExcel($complaints, $metrics, $filters);
        } else {
            return $this->generatePdf($complaints, $metrics, $filters);
        }
    }

    /**
     * Generate Excel report
     */
    private function generateExcel(array $complaints, array $metrics, array $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report');

        // Title
        $sheet->setCellValue('A1', 'CUSTOM REPORT');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Generated: ' . date('d/m/Y H:i'));

        // Filters info
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Report Filters:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        if (!empty($filters['date_from'])) {
            $sheet->setCellValue('A' . $row, 'Date From: ' . $filters['date_from']);
            $row++;
        }
        if (!empty($filters['date_to'])) {
            $sheet->setCellValue('A' . $row, 'Date To: ' . $filters['date_to']);
            $row++;
        }
        if (!empty($filters['application_id'])) {
            $app = $this->applicationModel->find($filters['application_id']);
            $sheet->setCellValue('A' . $row, 'Application: ' . ($app ? $app->name : '-'));
            $row++;
        }
        if (!empty($filters['status'])) {
            $sheet->setCellValue('A' . $row, 'Status: ' . ucfirst(str_replace('_', ' ', $filters['status'])));
            $row++;
        }

        // Data section
        $row += 2;
        $headerRow = $row;

        // Headers based on selected metrics
        $col = 'A';
        $headers = ['ID', 'Title', 'User', 'Email'];
        if (in_array('application', $metrics)) $headers[] = 'Application';
        if (in_array('category', $metrics)) $headers[] = 'Category';
        if (in_array('status', $metrics)) $headers[] = 'Status';
        if (in_array('priority', $metrics)) $headers[] = 'Priority';
        if (in_array('created', $metrics)) $headers[] = 'Created';
        if (in_array('resolved', $metrics)) $headers[] = 'Resolved';

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        // Data rows
        $row++;
        foreach ($complaints as $c) {
            $col = 'A';
            $user = $this->userModel->find($c->user_id);
            $app = $this->applicationModel->find($c->application_id);
            $cat = $this->categoryModel->find($c->category_id);

            $sheet->setCellValue($col++ . $row, $c->id);
            $sheet->setCellValue($col++ . $row, $c->title);
            $sheet->setCellValue($col++ . $row, $user ? $user->full_name : '-');
            $sheet->setCellValue($col++ . $row, $user ? $user->email : '-');

            if (in_array('application', $metrics)) $sheet->setCellValue($col++ . $row, $app ? $app->name : '-');
            if (in_array('category', $metrics)) $sheet->setCellValue($col++ . $row, $cat ? $cat->name : '-');
            if (in_array('status', $metrics)) $sheet->setCellValue($col++ . $row, ucfirst(str_replace('_', ' ', $c->status)));
            if (in_array('priority', $metrics)) $sheet->setCellValue($col++ . $row, ucfirst($c->priority));
            if (in_array('created', $metrics)) $sheet->setCellValue($col++ . $row, date('Y-m-d', strtotime($c->created_at)));
            if (in_array('resolved', $metrics)) $sheet->setCellValue($col++ . $row, $c->resolved_at ? date('Y-m-d', strtotime($c->resolved_at)) : '-');

            $row++;
        }

        // Auto-adjust columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Report_' . date('Y-m-d_His') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($writer->save('php://output'));
    }

    /**
     * Generate PDF report
     */
    private function generatePdf(array $complaints, array $metrics, array $filters)
    {
        $html = view('admin/reports/export_pdf', [
            'complaints' => $complaints,
            'metrics' => $metrics,
            'filters' => $filters,
            'userModel' => $this->userModel,
            'appModel' => $this->applicationModel,
            'catModel' => $this->categoryModel,
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Report_' . date('Y-m-d_His') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
}
