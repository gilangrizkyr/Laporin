<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \App\Validation\CustomRules::class, // Add custom rules
    ];

    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    // Login validation
    public array $login = [
        'email'    => 'required|valid_email',
        'password' => 'required|min_length[6]',
    ];

    public array $login_errors = [
        'email' => [
            'required'    => 'Email harus diisi',
            'valid_email' => 'Email tidak valid',
        ],
        'password' => [
            'required'   => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter',
        ],
    ];

    // Register validation
    public array $register = [
        'username'  => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'email'     => 'required|valid_email|is_unique[users.email]',
        'password'  => 'required|min_length[6]',
        'password_confirm' => 'required|matches[password]',
        'full_name' => 'required|min_length[3]|max_length[100]',
    ];

    public array $register_errors = [
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
        'password' => [
            'required'   => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter',
        ],
        'password_confirm' => [
            'required' => 'Konfirmasi password harus diisi',
            'matches'  => 'Konfirmasi password tidak cocok',
        ],
    ];

    // Complaint validation
    public array $complaint = [
        'application_id' => 'required|integer',
        'title'          => 'required|min_length[5]|max_length[200]',
        'description'    => 'required|min_length[10]',
        'impact_type'    => 'required|in_list[cannot_use,specific_bug,slow_performance,other]',
    ];

    public array $complaint_errors = [
        'application_id' => [
            'required' => 'Aplikasi harus dipilih',
            'integer'  => 'Aplikasi tidak valid',
        ],
        'title' => [
            'required'   => 'Judul laporan harus diisi',
            'min_length' => 'Judul minimal 5 karakter',
            'max_length' => 'Judul maksimal 200 karakter',
        ],
        'description' => [
            'required'   => 'Deskripsi harus diisi',
            'min_length' => 'Deskripsi minimal 10 karakter',
        ],
        'impact_type' => [
            'required' => 'Dampak harus dipilih',
            'in_list'  => 'Dampak tidak valid',
        ],
    ];

    // Feedback validation
    public array $feedback = [
        'rating'  => 'required|integer|greater_than[0]|less_than[6]',
        'comment' => 'permit_empty|max_length[500]',
    ];

    public array $feedback_errors = [
        'rating' => [
            'required'     => 'Rating harus diisi',
            'integer'      => 'Rating harus berupa angka',
            'greater_than' => 'Rating minimal 1',
            'less_than'    => 'Rating maksimal 5',
        ],
    ];
}