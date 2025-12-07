<?php

namespace App\Controllers\User;

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
     * Display chat interface
     */
    public function index($complaintId)
    {
        $userId = session()->get('user_id');
        
        // Get complaint
        $complaint = $this->complaintModel->find($complaintId);

        if (!$complaint) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Laporan tidak ditemukan');
        }

        // Check ownership
        if ($complaint->user_id != $userId) {
            return redirect()->to('user/complaints')
                           ->with('error', 'Anda tidak memiliki akses ke laporan ini');
        }

        // Get complaint info with relations
        $complaintInfo = $this->complaintModel
            ->select('complaints.*, applications.name as application_name')
            ->join('applications', 'applications.id = complaints.application_id')
            ->where('complaints.id', $complaintId)
            ->first();

        // Get admin info if assigned
        $admin = null;
        if ($complaint->assigned_to) {
            $admin = $this->userModel->find($complaint->assigned_to);
        }

        $data = [
            'title' => 'Chat - Laporan #' . $complaintId,
            'page_title' => 'Chat Internal - #' . $complaintId,
            'complaint' => $complaintInfo,
            'admin' => $admin,
        ];

        return view('user/chat/index', $data);
    }

    /**
     * Fetch chat messages (AJAX)
     */
    public function fetch($complaintId)
    {
        $userId = session()->get('user_id');
        
        // Verify ownership
        $complaint = $this->complaintModel->find($complaintId);
        if (!$complaint || $complaint->user_id != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        // Get chats with user info
        $chats = $this->chatModel
            ->select('chats.*, users.full_name, users.role')
            ->join('users', 'users.id = chats.user_id')
            ->where('chats.complaint_id', $complaintId)
            ->where('chats.is_internal_note', 0) // User tidak bisa lihat internal note admin
            ->orderBy('chats.created_at', 'ASC')
            ->findAll();

        $chatData = [];
        foreach ($chats as $chat) {
            $chatData[] = [
                'id' => $chat->id,
                'user_id' => $chat->user_id,
                'user_name' => $chat->full_name,
                'user_role' => $chat->role,
                'message' => $chat->message,
                'created_at' => date('d M Y, H:i', strtotime($chat->created_at)),
                'time_diff' => $chat->getTimeDiff(),
                'is_own' => $chat->user_id == $userId,
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'chats' => $chatData
        ]);
    }

    /**
     * Send chat message
     */
    public function send($complaintId)
    {
        $userId = session()->get('user_id');
        
        // Verify ownership
        $complaint = $this->complaintModel->find($complaintId);
        if (!$complaint || $complaint->user_id != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        // Validate message
        $message = $this->request->getPost('message');
        if (empty(trim($message))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesan tidak boleh kosong'
            ]);
        }

        // Insert chat
        $chatData = [
            'complaint_id' => $complaintId,
            'user_id' => $userId,
            'message' => $message,
            'is_internal_note' => 0,
        ];

        $chatId = $this->chatModel->insert($chatData);

        if (!$chatId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengirim pesan'
            ]);
        }

        // Send notification to admin (if assigned)
        if ($complaint->assigned_to) {
            $this->notificationService->notifyNewChatMessage(
                $complaint->assigned_to,
                $complaintId,
                session()->get('full_name')
            );
        } else {
            // Notify all admins if not assigned yet
            $this->notificationService->notifyAdmins(
                'Pesan Baru dari User',
                session()->get('full_name') . ' mengirim pesan pada laporan #' . $complaintId,
                $complaintId
            );
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pesan terkirim',
            'chat' => [
                'id' => $chatId,
                'user_name' => session()->get('full_name'),
                'user_role' => 'user',
                'message' => $message,
                'created_at' => date('d M Y, H:i'),
                'time_diff' => 'Baru saja',
                'is_own' => true,
            ]
        ]);
    }
}