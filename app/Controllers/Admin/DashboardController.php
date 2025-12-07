<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected $complaintModel;
    protected $userModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Get global statistics
        $stats = $this->complaintModel->getGlobalStats();

        // Get my assigned complaints
        $adminId = session()->get('user_id');
        $myComplaints = $this->complaintModel->getComplaintsByAdmin($adminId);

        // Get unassigned complaints
        $unassignedComplaints = $this->complaintModel->getUnassignedComplaints();

        // Get urgent complaints
        $urgentComplaints = $this->complaintModel->getUrgentComplaints();

        // Get recent complaints (all)
        $recentComplaints = $this->complaintModel
            ->select('complaints.*, users.full_name as user_name, applications.name as application_name')
            ->join('users', 'users.id = complaints.user_id')
            ->join('applications', 'applications.id = complaints.application_id')
            ->orderBy('complaints.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Dashboard Admin',
            'page_title' => 'Dashboard',
            'stats' => $stats,
            'myComplaints' => $myComplaints,
            'unassignedComplaints' => $unassignedComplaints,
            'urgentComplaints' => $urgentComplaints,
            'recentComplaints' => $recentComplaints,
        ];

        return view('admin/dashboard', $data);
    }
}