<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;

class ApplicationManagementController extends BaseController
{
    protected $appModel;

    public function __construct()
    {
        $this->appModel = new ApplicationModel();
    }

    public function index()
    {
        $apps = $this->appModel->orderBy('name', 'ASC')->findAll();
        return view('superadmin/applications/index', ['title' => 'Applications', 'page_title' => 'Application Management', 'apps' => $apps]);
    }

    public function create()
    {
        return view('superadmin/applications/form', ['title' => 'Create Application', 'page_title' => 'Create Application']);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        $data['is_critical'] = isset($data['is_critical']) ? 1 : 0;

        $insertId = $this->appModel->insert($data);
        if ($insertId === false) {
            $errors = $this->appModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('superadmin/applications'))->with('success', 'Application created');
    }

    public function edit($id)
    {
        $app = $this->appModel->find($id);
        if (!$app) return redirect()->to(base_url('superadmin/applications'))->with('error', 'Application not found');
        return view('superadmin/applications/form', ['title' => 'Edit Application', 'page_title' => 'Edit Application', 'app' => $app]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        $data['is_critical'] = isset($data['is_critical']) ? 1 : 0;

        $updated = $this->appModel->update($id, $data);
        if ($updated === false) {
            $errors = $this->appModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('superadmin/applications'))->with('success', 'Application updated');
    }

    public function delete($id)
    {
        if ($this->appModel->delete($id)) {
            return redirect()->to(base_url('superadmin/applications'))->with('success', 'Application deleted');
        }
        return redirect()->back()->with('error', 'Failed to delete application');
    }
}
