<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\ApplicationModel;
use App\Models\CategoryModel;
use App\Models\AttachmentModel;
use App\Models\ComplaintHistoryModel;
use App\Libraries\PriorityCalculator;
use App\Libraries\FileUploadHandler;
use App\Libraries\NotificationService;

class ComplaintController extends BaseController
{
    protected $complaintModel;
    protected $applicationModel;
    protected $categoryModel;
    protected $attachmentModel;
    protected $historyModel;
    protected $priorityCalculator;
    protected $fileUploadHandler;
    protected $notificationService;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->applicationModel = new ApplicationModel();
        $this->categoryModel = new CategoryModel();
        $this->attachmentModel = new AttachmentModel();
        $this->historyModel = new ComplaintHistoryModel();
        $this->priorityCalculator = new PriorityCalculator();
        $this->fileUploadHandler = new FileUploadHandler();
        $this->notificationService = new NotificationService();
    }

    /**
     * List all complaints by current user
     */
    public function index()
    {
        $userId = session()->get('user_id');
        
        // Get filter from query string
        $status = $this->request->getGet('status');
        $priority = $this->request->getGet('priority');
        
        // Get complaints
        $complaints = $this->complaintModel->getComplaintsByUser($userId, $status);
        
        // Filter by priority if provided
        if ($priority) {
            $complaints = array_filter($complaints, function($c) use ($priority) {
                return $c->priority === $priority;
            });
        }

        $data = [
            'title' => 'Daftar Laporan',
            'page_title' => 'Daftar Laporan Saya',
            'complaints' => $complaints,
            'currentStatus' => $status,
            'currentPriority' => $priority,
        ];

        return view('user/complaints/index', $data);
    }

    /**
     * Show create complaint form
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Laporan Baru',
            'page_title' => 'Buat Laporan Baru',
            'applications' => $this->applicationModel->getActiveApplications(),
            'categories' => $this->categoryModel->getActiveCategories(),
        ];

        return view('user/complaints/create', $data);
    }

    /**
     * Store new complaint
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'application_id' => 'required|integer',
            'category_id'    => 'permit_empty|integer',
            'title'          => 'required|min_length[5]|max_length[200]',
            'description'    => 'required|min_length[10]',
            'impact_type'    => 'required|in_list[cannot_use,specific_bug,slow_performance,other]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Calculate priority based on impact and application
        $priority = $this->priorityCalculator->calculate(
            $this->request->getPost('impact_type'),
            $this->request->getPost('application_id')
        );

        // Prepare complaint data
        $complaintData = [
            'user_id'        => session()->get('user_id'),
            'application_id' => $this->request->getPost('application_id'),
            'category_id'    => $this->request->getPost('category_id') ?: null,
            'title'          => $this->request->getPost('title'),
            'description'    => $this->request->getPost('description'),
            'impact_type'    => $this->request->getPost('impact_type'),
            'priority'       => $priority,
            'status'         => 'pending',
        ];

        // Insert complaint
        $complaintId = $this->complaintModel->insert($complaintData);

        if (!$complaintId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal membuat laporan. Silakan coba lagi.');
        }

        // Handle file uploads
        $files = $this->request->getFileMultiple('attachments');
        if ($files) {
            $uploadedFiles = $this->fileUploadHandler->uploadMultiple($files);
            
            foreach ($uploadedFiles as $fileData) {
                $this->attachmentModel->insert([
                    'complaint_id' => $complaintId,
                    'file_name'    => $fileData['file_name'],
                    'file_path'    => $fileData['file_path'],
                    'file_type'    => $fileData['file_type'],
                    'file_size'    => $fileData['file_size'],
                    'uploaded_by'  => session()->get('user_id'),
                ]);
            }
        }

        // Log history
        $this->historyModel->logAction(
            $complaintId,
            session()->get('user_id'),
            'created',
            null,
            null,
            'Laporan dibuat dengan prioritas: ' . $priority
        );

        // Send notification to admins
        $this->notificationService->notifyNewComplaint(
            $complaintId,
            $this->request->getPost('title')
        );

        return redirect()->to('user/complaints/' . $complaintId)
                       ->with('success', 'Laporan berhasil dibuat! Admin akan segera menindaklanjuti.');
    }

    /**
     * Show complaint detail
     */
    public function show($id)
    {
        $userId = session()->get('user_id');
        
        // Get complaint with relations
        $complaint = $this->complaintModel
            ->select('complaints.*, applications.name as application_name, categories.name as category_name, users.full_name as user_name')
            ->join('applications', 'applications.id = complaints.application_id')
            ->join('users', 'users.id = complaints.user_id')
            ->join('categories', 'categories.id = complaints.category_id', 'left')
            ->where('complaints.id', $id)
            ->first();

        if (!$complaint) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Laporan tidak ditemukan');
        }

        // Check ownership
        if ($complaint->user_id != $userId) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Anda tidak memiliki akses ke laporan ini');
        }

        // Get attachments
        $attachments = $this->attachmentModel->getAttachmentsByComplaint($id);

        // Get history
        $history = $this->historyModel
            ->select('complaint_history.*, users.full_name as user_name')
            ->join('users', 'users.id = complaint_history.user_id')
            ->where('complaint_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get admin info if assigned
        $admin = null;
        if ($complaint->assigned_to) {
            $userModel = new \App\Models\UserModel();
            $admin = $userModel->find($complaint->assigned_to);
        }

        $data = [
            'title' => 'Detail Laporan #' . $id,
            'page_title' => 'Detail Laporan #' . $id,
            'complaint' => $complaint,
            'attachments' => $attachments,
            'history' => $history,
            'admin' => $admin,
        ];

        return view('user/complaints/show', $data);
    }

    /**
     * Show edit form (only for pending complaints)
     */
    public function edit($id)
    {
        $userId = session()->get('user_id');
        $complaint = $this->complaintModel->find($id);

        if (!$complaint || $complaint->user_id != $userId) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Laporan tidak ditemukan');
        }

        if (!$complaint->isPending()) {
            return redirect()->to('user/complaints/' . $id)
                           ->with('error', 'Laporan yang sedang diproses tidak dapat diedit');
        }

        $data = [
            'title' => 'Edit Laporan',
            'page_title' => 'Edit Laporan #' . $id,
            'complaint' => $complaint,
            'applications' => $this->applicationModel->getActiveApplications(),
            'categories' => $this->categoryModel->getActiveCategories(),
        ];

        return view('user/complaints/edit', $data);
    }

    /**
     * Update complaint
     */
    public function update($id)
    {
        $userId = session()->get('user_id');
        $complaint = $this->complaintModel->find($id);

        if (!$complaint || $complaint->user_id != $userId || !$complaint->isPending()) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Tidak dapat mengupdate laporan ini');
        }

        // Validation
        $rules = [
            'application_id' => 'required|integer',
            'category_id'    => 'permit_empty|integer',
            'title'          => 'required|min_length[5]|max_length[200]',
            'description'    => 'required|min_length[10]',
            'impact_type'    => 'required|in_list[cannot_use,specific_bug,slow_performance,other]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Recalculate priority
        $priority = $this->priorityCalculator->calculate(
            $this->request->getPost('impact_type'),
            $this->request->getPost('application_id')
        );

        $updateData = [
            'application_id' => $this->request->getPost('application_id'),
            'category_id'    => $this->request->getPost('category_id') ?: null,
            'title'          => $this->request->getPost('title'),
            'description'    => $this->request->getPost('description'),
            'impact_type'    => $this->request->getPost('impact_type'),
            'priority'       => $priority,
        ];

        $this->complaintModel->update($id, $updateData);

        // Log history
        $this->historyModel->logAction(
            $id,
            $userId,
            'updated',
            null,
            null,
            'Laporan diupdate oleh user'
        );

        return redirect()->to('user/complaints/' . $id)
                       ->with('success', 'Laporan berhasil diupdate');
    }

    /**
     * Delete complaint (only pending)
     */
    public function delete($id)
    {
        $userId = session()->get('user_id');
        $complaint = $this->complaintModel->find($id);

        if (!$complaint || $complaint->user_id != $userId || !$complaint->isPending()) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Tidak dapat menghapus laporan ini');
        }

        // Delete attachments files
        $attachments = $this->attachmentModel->getAttachmentsByComplaint($id);
        foreach ($attachments as $attachment) {
            $this->fileUploadHandler->deleteFile($attachment->file_path);
        }

        // Delete complaint (cascade will delete attachments, chats, history)
        $this->complaintModel->delete($id);

        return redirect()->to('user/complaints')
                       ->with('success', 'Laporan berhasil dihapus');
    }
}
