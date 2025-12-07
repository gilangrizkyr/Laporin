<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display login form
     */
    public function index()
    {
        // Redirect if already logged in
        if (session()->has('user_id')) {
            return redirect()->to($this->getRedirectUrl());
        }

        $data = [
            'title' => 'Login - Sistem Pengaduan',
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login authentication
     */
    public function authenticate()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Get user by email
        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Email tidak terdaftar');
        }

        // Verify password
        if (!$user->verifyPassword($password)) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Password salah');
        }

        // Check if user is active
        if (!$user->isActive()) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // Set session
        $this->setUserSession($user);

        // Redirect based on role
        return redirect()->to($this->getRedirectUrl())
                       ->with('success', 'Selamat datang, ' . $user->full_name . '!');
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')
                       ->with('success', 'Anda telah logout');
    }

    /**
     * Set user session data
     */
    protected function setUserSession($user)
    {
        $sessionData = [
            'user_id'   => $user->id,
            'username'  => $user->username,
            'email'     => $user->email,
            'full_name' => $user->full_name,
            'role'      => $user->role,
            'logged_in' => true,
        ];

        session()->set($sessionData);
    }

    /**
     * Get redirect URL based on role
     */
    protected function getRedirectUrl(): string
    {
        $role = session()->get('role');

        switch ($role) {
            case 'superadmin':
                return base_url('superadmin/dashboard');
            case 'admin':
                return base_url('admin/dashboard');
            case 'user':
            default:
                return base_url('user/dashboard');
        }
    }
}