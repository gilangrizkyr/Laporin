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
            'Laporan ditugaskan ke ' . session()->get('full_name')
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
            'Status diubah dari ' . $oldStatus . ' menjadi ' . $newStatus
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
            'Prioritas diubah dari ' . $oldPriority . ' menjadi ' . $newPriority . ' oleh admin'
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
        // TODO: Implement PDF export
        return redirect()->back()
                       ->with('info', 'Fitur export PDF akan segera tersedia');
    }

    /**
     * Export complaints to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export
        return redirect()->back()
                       ->with('info', 'Fitur export Excel akan segera tersedia');
    }
}