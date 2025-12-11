<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class RegisterController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display register form
     */
    public function index()
    {
        // Redirect if already logged in
        if (session()->has('user_id')) {
            return redirect()->to($this->getRedirectUrl());
        }

        $data = [
            'title' => 'Register - Sistem Pengaduan',
        ];

        return view('auth/register', $data);
    }

    /**
     * Process registration
     */
    public function create()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'full_name'        => 'required|min_length[3]|max_length[100]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $errors = [
            'username' => [
                'required'   => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'is_unique'  => 'Username sudah digunakan',
            ],
            'email' => [
                'required'    => 'Email harus diisi',
                'valid_email' => 'Email tidak valid',
                'is_unique'   => 'Email sudah terdaftar',
            ],
            'full_name' => [
                'required'   => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
            ],
            'password' => [
                'required'   => 'Password harus diisi',
                'min_length' => 'Password minimal 6 karakter',
            ],
            'password_confirm' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches'  => 'Konfirmasi password tidak cocok',
            ],
        ];

        if (!$this->validate($rules, $errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Create user
        $userData = [
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'password'  => $this->request->getPost('password'),
            'role'      => 'user', // Default role
            'is_active' => 0,
        ];

        $userId = $this->userModel->insert($userData);

        if (!$userId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat akun. Silakan coba lagi.');
        }

        // === PESAN SUKSES YANG LEBIH JELAS ===
        return redirect()->to('auth/login')->with(
            'success',
            'Registrasi berhasil! Akun Anda sedang menunggu persetujuan. Silakan tunggu atau hubungi admin.'
        );

        // Tidak lagi auto-login
        // $this->setUserSession($user);   <-- HAPUS atau COMMENT
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
