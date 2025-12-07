<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\ChatModel;
use App\Models\UserModel;
use App\Libraries\NotificationService;

class ChatController extends BaseController
{
    protected $complaintModel;
    protected $chatModel;
    protected $userModel;
    protected $notificationService;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->chatModel = new ChatModel();
        $this->userModel = new UserModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Display chat interface (Admin side)
     */
    public function index($complaintId)
    {
        // Get complaint
        $complaint = $this->complaintModel
            ->select('complaints.*, applications.name as application_name, users.full_name as user_name')
            ->join('applications', 'applications.id = complaints.application_id')
            ->join('users', 'users.id = complaints.user_id')
            ->where('complaints.id', $complaintId)
            ->first();

        if (!$complaint) {
            return redirect()->to('admin/complaints')
                ->with('error', 'Laporan tidak ditemukan');
        }

        // Get user info
        $user = $this->userModel->find($complaint->user_id);

        $data = [
            'title' => 'Chat - Laporan #' . $complaintId,
            'page_title' => 'Chat Internal - #' . $complaintId,
            'complaint' => $complaint,
            'user' => $user,
        ];

        return view('admin/chat/index', $data);
    }

    /**
     * Fetch chat messages (AJAX) - Admin can see internal notes
     */
    public function fetch($complaintId)
    {
        // Verify complaint exists
        $complaint = $this->complaintModel->find($complaintId);
        if (!$complaint) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Complaint not found'
            ]);
        }

        // Get all chats including internal notes
        $chats = $this->chatModel
            ->select('chats.*, users.full_name, users.role')
            ->join('users', 'users.id = chats.user_id')
            ->where('chats.complaint_id', $complaintId)
            ->orderBy('chats.created_at', 'ASC')
            ->findAll();

        $currentUserId = session()->get('user_id');
        $chatData = [];

        foreach ($chats as $chat) {
            $chatData[] = [
                'id' => $chat->id,
                'user_id' => $chat->user_id,
                'user_name' => $chat->full_name,
                'user_role' => $chat->role,
                'message' => $chat->message,
                'is_internal_note' => $chat->is_internal_note,
                'created_at' => date('d M Y, H:i', strtotime($chat->created_at)),
                'time_diff' => $chat->getTimeDiff(),
                'is_own' => $chat->user_id == $currentUserId,
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'chats' => $chatData
        ]);
    }

    /**
     * Send chat message (Admin)
     */
    public function send($complaintId)
    {
        $complaint = $this->complaintModel->find($complaintId);
        if (!$complaint) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Complaint not found'
            ]);
        }

        // Validate message
        $message = $this->request->getPost('message');
        $isInternalNote = $this->request->getPost('is_internal_note') === '1';

        if (empty(trim($message))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesan tidak boleh kosong'
            ]);
        }

        // Insert chat
        $chatData = [
            'complaint_id' => $complaintId,
            'user_id' => session()->get('user_id'),
            'message' => $message,
            'is_internal_note' => $isInternalNote ? 1 : 0,
        ];

        $chatId = $this->chatModel->insert($chatData);

        if (!$chatId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengirim pesan'
            ]);
        }

        // Send notification to user (only if not internal note)
        if (!$isInternalNote) {
            $this->notificationService->notifyNewChatMessage(
                $complaint->user_id,
                $complaintId,
                session()->get('full_name')
            );
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $isInternalNote ? 'Internal note tersimpan' : 'Pesan terkirim',
            'chat' => [
                'id' => $chatId,
                'user_name' => session()->get('full_name'),
                'user_role' => session()->get('role'),
                'message' => $message,
                'is_internal_note' => $isInternalNote,
                'created_at' => date('d M Y, H:i'),
                'time_diff' => 'Baru saja',
                'is_own' => true,
            ]
        ]);
    }
}
