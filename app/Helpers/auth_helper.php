<?php

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in(): bool
    {
        return session()->has('user_id');
    }
}

if (!function_exists('get_user_id')) {
    /**
     * Get current user ID
     */
    function get_user_id(): ?int
    {
        return session()->get('user_id');
    }
}

if (!function_exists('get_user_role')) {
    /**
     * Get current user role
     */
    function get_user_role(): ?string
    {
        return session()->get('role');
    }
}

if (!function_exists('is_user')) {
    /**
     * Check if current user is regular user
     */
    function is_user(): bool
    {
        return session()->get('role') === 'user';
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin
     */
    function is_admin(): bool
    {
        return session()->get('role') === 'admin';
    }
}

if (!function_exists('is_superadmin')) {
    /**
     * Check if current user is superadmin
     */
    function is_superadmin(): bool
    {
        return session()->get('role') === 'superadmin';
    }
}

if (!function_exists('can_manage_users')) {
    /**
     * Check if user can manage users
     */
    function can_manage_users(): bool
    {
        return is_superadmin();
    }
}

if (!function_exists('can_handle_complaints')) {
    /**
     * Check if user can handle complaints
     */
    function can_handle_complaints(): bool
    {
        return is_admin() || is_superadmin();
    }
}