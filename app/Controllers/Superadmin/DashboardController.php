<?php

namespace App\Controllers\Superadmin;

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
        $stats = $this->complaintModel->getGlobalStats();
        $totalUsers = $this->userModel->countAllResults();

        $data = [
            'title' => 'Superadmin Dashboard',
            'page_title' => 'Superadmin Dashboard',
            'stats' => $stats,
            'totalUsers' => $totalUsers,
        ];

        return view('superadmin/dashboard', $data);
    }
}
