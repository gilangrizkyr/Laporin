<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Attachment;

class AttachmentModel extends Model
{
    protected $table            = 'attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Attachment::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'complaint_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getAttachmentsByComplaint(int $complaintId): array
    {
        return $this->where('complaint_id', $complaintId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function deleteAttachment(int $id): bool
    {
        $attachment = $this->find($id);
        
        if ($attachment && file_exists(FCPATH . $attachment->file_path)) {
            unlink(FCPATH . $attachment->file_path);
        }
        
        return $this->delete($id);
    }
}