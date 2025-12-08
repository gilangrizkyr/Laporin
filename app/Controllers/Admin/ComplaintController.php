<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\ApplicationModel;
use App\Models\CategoryModel;
use App\Models\AttachmentModel;
use App\Models\ComplaintHistoryModel;
use App\Models\FeedbackModel;
use App\Models\UserModel;
use App\Libraries\NotificationService;

class ComplaintController extends BaseController
{
    protected $complaintModel;
    protected $applicationModel;
    protected $categoryModel;
    protected $attachmentModel;
    protected $historyModel;
    protected $feedbackModel;
    protected $userModel;
    protected $notificationService;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->applicationModel = new ApplicationModel();
        $this->categoryModel = new CategoryModel();
        $this->attachmentModel = new AttachmentModel();
        $this->historyModel = new ComplaintHistoryModel();
        $this->feedbackModel = new FeedbackModel();
        $this->userModel = new UserModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * List all complaints with advanced filters
     */
    public function index()
    {
        // Get filters from query string
        $filters = [
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'application_id' => $this->request->getGet('application_id'),
            'assigned_to' => $this->request->getGet('assigned_to'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        // Special filter for unassigned
        $showUnassigned = $this->request->getGet('assigned') === 'unassigned';

        // Get complaints
        if ($showUnassigned) {
            $complaints = $this->complaintModel->getUnassignedComplaints();
        } else {
            $complaints = $this->complaintModel->getAllComplaints($filters);
        }

        // Get data for filter options
        $applications = $this->applicationModel->getActiveApplications();
        $admins = $this->userModel->getAllAdmins();

        $data = [
            'title' => 'Kelola Laporan',
            'page_title' => 'Kelola Semua Laporan',
            'complaints' => $complaints,
            'applications' => $applications,
            'admins' => $admins,
            'filters' => $filters,
            'showUnassigned' => $showUnassigned,
        ];

        return view('admin/complaints/index', $data);
    }

    /**
     * Show complaint detail
     */
    public function show($id)
    {
        // Get complaint with relations
        $complaint = $this->complaintModel
            ->select('complaints.*, applications.name as application_name, categories.name as category_name, users.full_name as user_name, users.email as user_email')
            ->join('applications', 'applications.id = complaints.application_id')
            ->join('users', 'users.id = complaints.user_id')
            ->join('categories', 'categories.id = complaints.category_id', 'left')
            ->where('complaints.id', $id)
            ->first();

        if (!$complaint) {
            return redirect()->to('admin/complaints')
                ->with('error', 'Laporan tidak ditemukan');
        }

        // Get user info
        $user = $this->userModel->find($complaint->user_id);

        // Get attachments
        $attachments = $this->attachmentModel->getAttachmentsByComplaint($id);

        // Get history
        $history = $this->historyModel
            ->select('complaint_history.*, users.full_name as user_name, users.role')
            ->join('users', 'users.id = complaint_history.user_id')
            ->where('complaint_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get admin info if assigned
        $admin = null;
        if ($complaint->assigned_to) {
            $admin = $this->userModel->find($complaint->assigned_to);
        }

        // Get feedback if exists
        $feedback = $this->feedbackModel->getFeedbackByComplaint($id);

        // Get all admins for assignment
        $allAdmins = $this->userModel->getAllAdmins();

        $data = [
            'title' => 'Detail Laporan #' . $id,
            'page_title' => 'Detail Laporan #' . $id,
            'complaint' => $complaint,
            'user' => $user,
            'attachments' => $attachments,
            'history' => $history,
            'admin' => $admin,
            'feedback' => $feedback,
            'allAdmins' => $allAdmins,
        ];

        return view('admin/complaints/show', $data);
    }

    /**
     * Assign complaint to admin
     */
    public function assign($id)
    {
        $complaint = $this->complaintModel->find($id);

        if (!$complaint) {
            return redirect()->to('admin/complaints')
                ->with('error', 'Laporan tidak ditemukan');
        }

        $adminId = session()->get('user_id');

        // Update complaint
        $this->complaintModel->update($id, [
            'assigned_to' => $adminId,
            'status' => 'in_progress',
        ]);

        // Log history
        $this->historyModel->logAction(
            $id,
            $adminId,
            'assigned',
            null,
            session()->get('full_name'),
            'Laporan ditugaskan ke ' . session()->get('full_name'),
            session()->get('full_name'),
            session()->get('email')
        );

        // Notify user
        $this->notificationService->notifyAssigned(
            $complaint->user_id,
            $id,
            session()->get('full_name')
        );

        return redirect()->to('admin/complaints/' . $id)
            ->with('success', 'Laporan berhasil di-assign ke Anda');
    }

    /**
     * Change complaint status
     */
    public function changeStatus($id)
    {
        $complaint = $this->complaintModel->find($id);

        if (!$complaint) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ]);
        }

        $newStatus = $this->request->getPost('status');
        $validStatuses = ['pending', 'in_progress', 'resolved', 'closed'];

        if (!in_array($newStatus, $validStatuses)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid'
            ]);
        }

        $oldStatus = $complaint->status;

        // Update status
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'resolved') {
            $updateData['resolved_at'] = date('Y-m-d H:i:s');
        } elseif ($newStatus === 'closed') {
            $updateData['closed_at'] = date('Y-m-d H:i:s');
        }

        $this->complaintModel->update($id, $updateData);

        // Log history
        $this->historyModel->logAction(
            $id,
            session()->get('user_id'),
            'status_changed',
            $oldStatus,
            $newStatus,
            'Status diubah dari ' . $oldStatus . ' menjadi ' . $newStatus,
            session()->get('full_name'),
            session()->get('email')
        );

        // Notify user
        $this->notificationService->notifyStatusChange(
            $complaint->user_id,
            $id,
            $oldStatus,
            $newStatus
        );

        // Special notification for resolved
        if ($newStatus === 'resolved') {
            $this->notificationService->notifyResolved($complaint->user_id, $id);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status berhasil diubah'
        ]);
    }

    /**
     * Change complaint priority
     */
    public function changePriority($id)
    {
        $complaint = $this->complaintModel->find($id);

        if (!$complaint) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ]);
        }

        $newPriority = $this->request->getPost('priority');
        $validPriorities = ['normal', 'important', 'urgent'];

        if (!in_array($newPriority, $validPriorities)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Prioritas tidak valid'
            ]);
        }

        $oldPriority = $complaint->priority;

        // Update priority
        $this->complaintModel->update($id, [
            'priority' => $newPriority
        ]);

        // Log history
        $this->historyModel->logAction(
            $id,
            session()->get('user_id'),
            'priority_changed',
            $oldPriority,
            $newPriority,
            'Prioritas diubah dari ' . $oldPriority . ' menjadi ' . $newPriority . ' oleh admin',
            session()->get('full_name'),
            session()->get('email')
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Prioritas berhasil diubah'
        ]);
    }

    /**
     * Export single complaint to PDF
     */
    public function exportPdf($id)
    {
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            return $this->response->setStatusCode(404)->setBody('Complaint not found');
        }

        $user = $this->userModel->find($complaint->user_id);
        $app = $this->applicationModel->find($complaint->application_id);
        $category = $this->categoryModel->find($complaint->category_id);
        $assignedTo = $complaint->assigned_to ? $this->userModel->find($complaint->assigned_to) : null;
        $attachments = $this->attachmentModel->where('complaint_id', $id)->findAll();
        $history = $this->historyModel->where('complaint_id', $id)->orderBy('created_at', 'DESC')->findAll();
        $feedback = $this->feedbackModel->where('complaint_id', $id)->first();

        // Generate HTML for PDF
        $html = view('admin/complaints/export_pdf', [
            'complaint' => $complaint,
            'user' => $user,
            'app' => $app,
            'category' => $category,
            'assignedTo' => $assignedTo,
            'attachments' => $attachments,
            'history' => $history,
            'feedback' => $feedback,
        ]);

        // Generate PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Laporan_' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT) . '_' . date('Y-m-d') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Export filtered complaints to Excel
     */
    public function exportExcel()
    {
        // Get filters
        $filters = [
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'application_id' => $this->request->getGet('application_id'),
            'assigned_to' => $this->request->getGet('assigned_to'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        $complaints = $this->complaintModel->getAllComplaints($filters);

        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keluhan');

        // Header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Pengguna');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Aplikasi');
        $sheet->setCellValue('E1', 'Kategori');
        $sheet->setCellValue('F1', 'Judul');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Prioritas');
        $sheet->setCellValue('I1', 'Ditugaskan ke');
        $sheet->setCellValue('J1', 'Tanggal Dibuat');
        $sheet->setCellValue('K1', 'Tanggal Diselesaikan');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1976D2']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Data rows
        $row = 2;
        foreach ($complaints as $c) {
            $user = $this->userModel->find($c->user_id);
            $app = $this->applicationModel->find($c->application_id);
            $category = $this->categoryModel->find($c->category_id);
            $assignedUser = $c->assigned_to ? $this->userModel->find($c->assigned_to) : null;

            $sheet->setCellValue('A' . $row, $c->id);
            $sheet->setCellValue('B' . $row, $user ? $user->full_name : '-');
            $sheet->setCellValue('C' . $row, $user ? $user->email : '-');
            $sheet->setCellValue('D' . $row, $app ? $app->name : '-');
            $sheet->setCellValue('E' . $row, $category ? $category->name : '-');
            $sheet->setCellValue('F' . $row, $c->title);
            $sheet->setCellValue('G' . $row, ucfirst(str_replace('_', ' ', $c->status)));
            $sheet->setCellValue('H' . $row, ucfirst($c->priority));
            $sheet->setCellValue('I' . $row, $assignedUser ? $assignedUser->full_name : '-');
            $sheet->setCellValue('J' . $row, date('Y-m-d H:i', strtotime($c->created_at)));
            $sheet->setCellValue('K' . $row, $c->resolved_at ? date('Y-m-d H:i', strtotime($c->resolved_at)) : '-');

            $row++;
        }

        // Auto-adjust column widths
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Keluhan_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }
}
