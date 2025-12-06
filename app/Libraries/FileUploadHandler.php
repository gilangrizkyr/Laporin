<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class FileUploadHandler
{
    protected $uploadPath = 'uploads/complaints/';
    protected $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif'],
        'video' => ['mp4', 'avi', 'mpeg', 'mov'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ];
    protected $maxSize = [
        'image' => 5120,      // 5MB in KB
        'video' => 51200,     // 50MB in KB
        'document' => 10240,  // 10MB in KB
    ];

    public function __construct()
    {
        // Create upload directory if not exists
        if (!is_dir(FCPATH . $this->uploadPath)) {
            mkdir(FCPATH . $this->uploadPath, 0777, true);
        }
    }

    /**
     * Upload single file
     * 
     * @param UploadedFile $file
     * @return array|false Returns file info or false on failure
     */
    public function upload(UploadedFile $file)
    {
        if (!$file->isValid()) {
            return false;
        }

        // Validate file
        if (!$this->validateFile($file)) {
            return false;
        }

        // Generate unique filename
        $newName = $this->generateFileName($file);
        
        // Move file
        if ($file->move(FCPATH . $this->uploadPath, $newName)) {
            return [
                'file_name' => $file->getClientName(),
                'file_path' => $this->uploadPath . $newName,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        return false;
    }

    /**
     * Upload multiple files
     * 
     * @param array $files Array of UploadedFile
     * @return array Array of uploaded file info
     */
    public function uploadMultiple(array $files): array
    {
        $uploaded = [];

        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $result = $this->upload($file);
                if ($result) {
                    $uploaded[] = $result;
                }
            }
        }

        return $uploaded;
    }

    /**
     * Validate file type and size
     */
    protected function validateFile(UploadedFile $file): bool
    {
        $extension = $file->getClientExtension();
        $fileType = $this->getFileType($extension);
        
        if (!$fileType) {
            return false;
        }

        // Check file size
        $fileSizeKB = $file->getSize() / 1024;
        if ($fileSizeKB > $this->maxSize[$fileType]) {
            return false;
        }

        return true;
    }

    /**
     * Get file type category
     */
    protected function getFileType(string $extension): ?string
    {
        foreach ($this->allowedTypes as $type => $extensions) {
            if (in_array(strtolower($extension), $extensions)) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Generate unique filename
     */
    protected function generateFileName(UploadedFile $file): string
    {
        return time() . '_' . uniqid() . '.' . $file->getClientExtension();
    }

    /**
     * Delete file from server
     */
    public function deleteFile(string $filePath): bool
    {
        $fullPath = FCPATH . $filePath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Get allowed file types for validation message
     */
    public function getAllowedTypesString(): string
    {
        $all = [];
        foreach ($this->allowedTypes as $extensions) {
            $all = array_merge($all, $extensions);
        }
        return implode(', ', $all);
    }

    /**
     * Get max file size by type
     */
    public function getMaxSizeByType(string $type): int
    {
        return $this->maxSize[$type] ?? 0;
    }

    /**
     * Format file size to human readable
     */
    public function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}