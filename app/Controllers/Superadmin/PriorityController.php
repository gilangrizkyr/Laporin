<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\ComplaintHistoryModel;

class PriorityController extends BaseController
{
    protected $complaintModel;
    protected $historyModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->historyModel = new ComplaintHistoryModel();
    }

    public function override($id)
    {
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            return redirect()->back()->with('error', 'Complaint not found');
        }

        if ($this->request->getMethod() === 'post') {
            $newPriority = $this->request->getPost('priority');
            $reason = $this->request->getPost('reason');
            $old = $complaint->priority;
            $this->complaintModel->update($id, ['priority' => $newPriority]);
            $this->historyModel->logAction(
                $id,
                session()->get('user_id'),
                'priority_override',
                $old,
                $newPriority,
                $reason,
                session()->get('full_name'),
                session()->get('email')
            );
            return redirect()->to(base_url('admin/complaints/' . $id))->with('success', 'Priority overridden');
        }

        return view('superadmin/priority_override', ['complaint' => $complaint]);
    }
}
