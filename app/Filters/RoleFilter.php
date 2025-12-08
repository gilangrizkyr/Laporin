<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in first
        if (!session()->has('user_id')) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Silakan login terlebih dahulu');
        }

        // Get user role from session
        $userRole = strtolower((string) session()->get('role'));

        // If no arguments provided, allow all authenticated users
        if (empty($arguments)) {
            return;
        }

        // Normalize arguments into array (handles cases like 'admin,superadmin')
        if (is_string($arguments)) {
            $args = array_filter(array_map('trim', explode(',', $arguments)));
        } elseif (is_array($arguments)) {
            $args = $arguments;
        } else {
            $args = (array) $arguments;
        }

        // Lowercase compare for robustness
        $allowed = array_map('strtolower', $args);

        // Check if user role is in allowed roles
        if (!in_array($userRole, $allowed, true)) {
            // Redirect based on current role
            switch ($userRole) {
                case 'superadmin':
                    return redirect()->to('/superadmin/dashboard')
                                   ->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
                case 'admin':
                    return redirect()->to('/admin/dashboard')
                                   ->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
                case 'user':
                default:
                    return redirect()->to('/user/dashboard')
                                   ->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}