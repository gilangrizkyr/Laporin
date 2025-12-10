<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    // Halaman daftar notifikasi user
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;

        $notifications = $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        return view('user/notifications/list', [
            'title' => 'Notifikasi Saya',
            'notifications' => $notifications,
            'pager' => $this->model->pager,
        ]);
    }

    // Tandai sebagai dibaca
    public function markAsRead($id)
    {
        $userId = session()->get('user_id');

        $notif = $this->model->find($id);
        if (!$notif || $notif->user_id != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $this->model->markAsRead($id);
        return $this->response->setJSON(['success' => true]);
    }

    // Tandai semua sebagai dibaca
    public function markAllAsRead()
    {
        $userId = session()->get('user_id');

        $this->model->markAllAsRead($userId);
        return $this->response->setJSON(['success' => true]);
    }

    // Mendapatkan jumlah notifikasi yang belum dibaca
    public function getUnreadCount()
    {
        $userId = session()->get('user_id');
        $count = $this->model->where('user_id', $userId)
                             ->where('is_read', false) // Hanya yang belum dibaca
                             ->countAllResults(); // Menghitung jumlah

        return $this->response->setJSON(['count' => $count]);
    }

    // Mendapatkan 5 notifikasi terbaru
    public function getRecentNotifications()
    {
        $userId = session()->get('user_id');
        $notifications = $this->model->where('user_id', $userId)
                                     ->where('is_read', false) // Hanya yang belum dibaca
                                     ->orderBy('created_at', 'DESC')
                                     ->findAll(5); // Mengambil 5 notifikasi terbaru

        return $this->response->setJSON(['notifications' => $notifications]);
    }
}
