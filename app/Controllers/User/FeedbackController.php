<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\FeedbackModel;
use App\Models\ComplaintHistoryModel;

class FeedbackController extends BaseController
{
    protected $complaintModel;
    protected $feedbackModel;
    protected $historyModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->feedbackModel = new FeedbackModel();
        $this->historyModel = new ComplaintHistoryModel();
    }

    /**
     * Show feedback form
     */
    public function create($complaintId)
    {
        $userId = session()->get('user_id');
        
        // Get complaint
        $complaint = $this->complaintModel
            ->select('complaints.*, applications.name as application_name')
            ->join('applications', 'applications.id = complaints.application_id')
            ->where('complaints.id', $complaintId)
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

        // Check if complaint is resolved or closed
        if (!$complaint->isResolved() && !$complaint->isClosed()) {
            return redirect()->to('user/complaints/' . $complaintId)
                           ->with('error', 'Feedback hanya dapat diberikan untuk laporan yang sudah diselesaikan');
        }

        // Check if feedback already exists
        $existingFeedback = $this->feedbackModel->getFeedbackByComplaint($complaintId);
        if ($existingFeedback) {
            return redirect()->to('user/complaints/' . $complaintId)
                           ->with('warning', 'Anda sudah memberikan feedback untuk laporan ini');
        }

        $data = [
            'title' => 'Beri Feedback - Laporan #' . $complaintId,
            'page_title' => 'Beri Feedback',
            'complaint' => $complaint,
        ];

        return view('user/feedback/create', $data);
    }

    /**
     * Store feedback
     */
    public function store($complaintId)
    {
        $userId = session()->get('user_id');
        
        // Verify complaint
        $complaint = $this->complaintModel->find($complaintId);
        
        if (!$complaint || $complaint->user_id != $userId) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Tidak dapat memberikan feedback');
        }

        if (!$complaint->isResolved() && !$complaint->isClosed()) {
            return redirect()->to('user/complaints/' . $complaintId)
                           ->with('error', 'Feedback hanya dapat diberikan untuk laporan yang sudah diselesaikan');
        }

        // Check if feedback already exists
        $existingFeedback = $this->feedbackModel->getFeedbackByComplaint($complaintId);
        if ($existingFeedback) {
            return redirect()->to('user/complaints/' . $complaintId)
                           ->with('warning', 'Anda sudah memberikan feedback untuk laporan ini');
        }

        // Validate
        $rules = [
            'rating'  => 'required|integer|greater_than[0]|less_than[6]',
            'comment' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Insert feedback
        $feedbackData = [
            'complaint_id' => $complaintId,
            'user_id'      => $userId,
            'rating'       => $this->request->getPost('rating'),
            'comment'      => $this->request->getPost('comment'),
        ];

        $feedbackId = $this->feedbackModel->insert($feedbackData);

        if (!$feedbackId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan feedback. Silakan coba lagi.');
        }

        // Auto-close ticket after feedback
        $this->complaintModel->update($complaintId, [
            'status' => 'closed',
            'closed_at' => date('Y-m-d H:i:s'),
        ]);

        // Log history
        $this->historyModel->logAction(
            $complaintId,
            $userId,
            'closed',
            'resolved',
            'closed',
            'Laporan ditutup setelah user memberikan feedback (Rating: ' . $this->request->getPost('rating') . '/5)'
        );

        return redirect()->to('user/complaints/' . $complaintId)
                       ->with('success', 'Terima kasih atas feedback Anda! Laporan telah ditutup.');
    }
}