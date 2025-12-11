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
        $this->complaintModel   = new ComplaintModel();
        $this->applicationModel = new ApplicationModel();
        $this->categoryModel    = new CategoryModel();
        $this->userModel        = new UserModel();
    }

    public function index()
    {
        $data = [
            'title'        => 'Custom Report Generator',
            'applications' => $this->applicationModel->findAll(),
            'categories'   => $this->categoryModel->findAll(),
            'admins'       => $this->userModel->whereIn('role', ['admin', 'superadmin'])->findAll(),
        ];
        return view('admin/reports/builder', $data);
    }

    public function generate()
    {
        $metrics    = $this->request->getPost('metrics') ?? [];
        $dateFrom   = $this->request->getPost('date_from');
        $dateTo     = $this->request->getPost('date_to');
        $appId      = $this->request->getPost('app_id');
        $catId      = $this->request->getPost('category_id');
        $status     = $this->request->getPost('status');
        $priority   = $this->request->getPost('priority');
        $assignedTo = $this->request->getPost('assigned_to');
        $format     = $this->request->getPost('format') ?? 'pdf';

        $filters = array_filter([
            'date_from'     => $dateFrom,
            'date_to'       => $dateTo ? $dateTo . ' 23:59:59' : null,
            'application_id' => $appId,
            'category_id'   => $catId,
            'status'        => $status,
            'priority'      => $priority,
            'assigned_to'   => $assignedTo,
        ]);

        // PAKAI METHOD BARU → LANGSUNG DAPAT NAMA LENGKAP & NAMA APP
        $complaints = $this->complaintModel->getFilteredComplaintsWithRelations($filters);

        session()->set('report_data', [
            'complaints' => $complaints,
            'metrics'    => $metrics,
            'filters'    => $filters,
            'format'     => $format,
        ]);

        return view('admin/reports/preview', [
            'title'      => 'Report Preview',
            'complaints' => $complaints,
            'metrics'    => $metrics,
            'filters'    => $filters,
            'format'     => $format,
        ]);
    }

    public function download(string $format)
    {
        $data = session()->get('report_data');
        if (!$data) return redirect()->to('admin/reports')->with('error', 'No report data');

        if ($format === 'excel') {
            return $this->generateExcel($data['complaints'], $data['metrics'], $data['filters']);
        } else {
            return $this->generatePdf($data['complaints'], $data['metrics'], $data['filters']);
        }
    }

    private function generateExcel($complaints, $metrics, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN PENGADUAN SISTEM');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $row = 3;
        $sheet->setCellValue("A{$row}", "Dibuat pada: " . date('d F Y H:i'));
        $row += 2;

        // Header tabel
        $headers = ['No', 'Judul Pengaduan', 'Pengadu'];
        if (in_array('application', $metrics)) $headers[] = 'Aplikasi';
        if (in_array('category', $metrics)) $headers[] = 'Kategori';
        if (in_array('status', $metrics)) $headers[] = 'Status';
        if (in_array('priority', $metrics)) $headers[] = 'Prioritas';
        if (in_array('created', $metrics)) $headers[] = 'Tanggal Dibuat';

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col++ . $row, $h);
        }
        $sheet->getStyle("A{$row}:" . $col . $row)->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:" . $col . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE4E4E4');

        // Isi data
        $no = 1;
        $row++;
        foreach ($complaints as $c) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $no++);
            $sheet->setCellValue($col++ . $row, $c->title);
            $sheet->setCellValue($col++ . $row, $c->user_full_name ?? 'Unknown User');
            if (in_array('application', $metrics)) $sheet->setCellValue($col++ . $row, $c->application_name ?? '-');
            if (in_array('category', $metrics)) $sheet->setCellValue($col++ . $row, $c->category_name ?? '-');
            if (in_array('status', $metrics)) $sheet->setCellValue($col++ . $row, ucfirst(str_replace('_', ' ', $c->status)));
            if (in_array('priority', $metrics)) $sheet->setCellValue($col++ . $row, ucfirst($c->priority));
            if (in_array('created', $metrics)) $sheet->setCellValue($col++ . $row, date('d-m-Y', strtotime($c->created_at)));
            $row++;
        }

        foreach (range('A', $col) as $c) $sheet->getColumnDimension($c)->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Pengaduan_' . date('Ymd_His') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($writer->save('php://output'));
    }

    private function generatePdf($complaints, $metrics, $filters)
    {
        $html = view('admin/reports/export_pdf', [
            'complaints' => $complaints,
            'metrics'    => $metrics,
            'filters'    => $filters,
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Laporan_Pengaduan_' . date('Ymd_His') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            // UBAH DARI attachment → inline !
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
}
