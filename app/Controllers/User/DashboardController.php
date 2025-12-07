<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;

class DashboardController extends BaseController
{
    protected $complaintModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');

        // Get user complaint statistics
        $stats = $this->complaintModel->getUserComplaintStats($userId);

        // Get recent complaints (last 5)
        $recentComplaints = $this->complaintModel->getComplaintsByUser($userId);
        $recentComplaints = array_slice($recentComplaints, 0, 5);

        $data = [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard',
            'stats' => $stats,
            'recentComplaints' => $recentComplaints,
        ];

        return view('user/dashboard', $data);
    }
}