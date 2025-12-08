<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    // Get unread count for current user (AJAX)
    public function getUnreadCount()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['count' => 0]);
        }

        $count = $this->model->countUnread((int) $userId);
        return $this->response->setJSON(['count' => $count]);
    }

    // Get recent notifications list (AJAX)
    public function getRecent()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['notifications' => []]);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $notifications = $this->model->getNotificationsByUser((int) $userId, false);
        $notifications = array_slice($notifications, 0, $limit); // ambil sesuai limit


        return $this->response->setJSON(['notifications' => $notifications]);
    }

    // Mark notification as read (AJAX)
    public function markRead(int $id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $notification = $this->model->find($id);
        if (!$notification || $notification->user_id != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }


        $this->model->markAsRead($id);
        return $this->response->setJSON(['success' => true]);
    }

    // Mark all as read (AJAX)
    public function markAllRead()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $this->model->markAllAsRead((int) $userId);
        return $this->response->setJSON(['success' => true]);
    }

    // Get notification page (HTML)
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to(route_to('login'));
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;

        try {
            $notifications = $this->model->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;
        } catch (\Exception $e) {
            $notifications = [];
            $pager = null;
        }

        return view('admin/notifications/list', [
            'title' => 'Notifications',
            'notifications' => $notifications,
            'pager' => $pager,
        ]);
    }

    // Delete notification
    public function delete(int $id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $notification = $this->model->find($id);
        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $this->model->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
