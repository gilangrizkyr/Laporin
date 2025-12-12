<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserManagementController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('created_at', 'DESC')->findAll();
        return view('superadmin/users/index', [
            'title' => 'Users',
            'page_title' => 'User Management',
            'users' => $users
        ]);
    }

    public function create()
    {
        return view('superadmin/users/form', [
            'title' => 'Create User',
            'page_title' => 'Create User'
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        if (empty($data['password'])) {
            return redirect()->back()->withInput()->with('errors', ['Password harus diisi']);
        }

        $insertId = $this->userModel->insert($data);

        if ($insertId === false) {
            $errors = $this->userModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('superadmin/users'))->with('success', 'User created');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) return redirect()->to(base_url('superadmin/users'))->with('error', 'User not found');

        return view('superadmin/users/form', [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'user' => $user
        ]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        // validasi email unik kecuali ID sendiri
        $existingEmail = $this->userModel->where('email', $data['email'])->first();
        if ($existingEmail && $existingEmail->id != $id) {
            return redirect()->back()->withInput()->with('errors', ['Email sudah terdaftar']);
        }

        // validasi username unik kecuali ID sendiri
        $existingUsername = $this->userModel->where('username', $data['username'])->first();
        if ($existingUsername && $existingUsername->id != $id) {
            return redirect()->back()->withInput()->with('errors', ['Username sudah digunakan']);
        }

        $updated = $this->userModel->update($id, $data);

        if ($updated === false) {
            $errors = $this->userModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('superadmin/users'))->with('success', 'User updated');
    }

    public function delete($id)
    {
        if ($this->userModel->delete($id)) {
            return redirect()->to(base_url('superadmin/users'))->with('success', 'User deleted');
        }

        return redirect()->back()->with('error', 'Failed to delete user');
    }

  
    public function toggleActive($id)
    {
        if ($this->userModel->toggleActive($id)) {
            return redirect()->to(base_url('superadmin/users'))->with('success', 'User toggled');
        }
        return redirect()->back()->with('error', 'Failed to toggle user');
    }
}
