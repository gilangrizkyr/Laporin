<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Validate if email is unique except for current user
     */
    public function unique_email(string $str, string $fields, array $data): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        
        $builder->where('email', $str);
        
        // If updating, exclude current user
        if (isset($data['id'])) {
            $builder->where('id !=', $data['id']);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Validate if username is unique except for current user
     */
    public function unique_username(string $str, string $fields, array $data): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        
        $builder->where('username', $str);
        
        // If updating, exclude current user
        if (isset($data['id'])) {
            $builder->where('id !=', $data['id']);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Validate file upload
     */
    public function valid_file_upload(string $str, string $fields, array $data): bool
    {
        // This will be handled by FileUploadHandler
        // Just return true here
        return true;
    }

    /**
     * Validate if complaint belongs to user
     */
    public function owns_complaint(string $str, string $fields, array $data): bool
    {
        $userId = session()->get('user_id');
        $complaintId = $str;
        
        $db = \Config\Database::connect();
        $complaint = $db->table('complaints')
                       ->where('id', $complaintId)
                       ->where('user_id', $userId)
                       ->get()
                       ->getRow();
        
        return $complaint !== null;
    }
}