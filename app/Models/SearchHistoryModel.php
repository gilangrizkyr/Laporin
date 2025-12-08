<?php

namespace App\Models;

use CodeIgniter\Model;

class SearchHistoryModel extends Model
{
    protected $table = 'search_history';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'query', 'filters', 'results_count', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $returnType = 'array';
}
