<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComplaintModel;
use App\Models\KnowledgeBaseModel;
use App\Models\UserModel;
use App\Models\SearchHistoryModel;

class SearchController extends BaseController
{
    protected $complaintModel;
    protected $kbModel;
    protected $userModel;
    protected $historyModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->kbModel = new KnowledgeBaseModel();
        $this->userModel = new UserModel();
        $this->historyModel = new SearchHistoryModel();
    }

    // Search page and results with advanced filters
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $type = $this->request->getGet('type') ?? 'all'; // all | complaints | kb | users

        // Advanced filters
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $appId = $this->request->getGet('app_id');
        $catId = $this->request->getGet('category_id');
        $status = $this->request->getGet('status');
        $priority = $this->request->getGet('priority');

        $results = [
            'complaints' => [],
            'kb' => [],
            'users' => [],
        ];

        // Fetch available options for filter UI
        $applicationModel = new \App\Models\ApplicationModel();
        $categoryModel = new \App\Models\CategoryModel();
        $applications = $applicationModel->findAll();
        $categories = $categoryModel->findAll();

        if ($q !== '') {
            // Search complaints with filters
            if ($type === 'complaints' || $type === 'all') {
                $builder = $this->complaintModel->like('title', $q)->orLike('description', $q);

                if ($dateFrom) {
                    $builder->where('created_at >=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $builder->where('created_at <=', $dateTo . ' 23:59:59');
                }
                if ($appId) {
                    $builder->where('application_id', $appId);
                }
                if ($catId) {
                    $builder->where('category_id', $catId);
                }
                if ($status) {
                    $builder->where('status', $status);
                }
                if ($priority) {
                    $builder->where('priority', $priority);
                }

                $results['complaints'] = $builder->orderBy('created_at', 'DESC')->findAll();
            }

            // Search KB articles
            if ($type === 'kb' || $type === 'all') {
                $builder = $this->kbModel->like('title', $q)->orLike('content', $q)->where('is_published', 1);

                if ($catId) {
                    $builder->where('category_id', $catId);
                }

                $results['kb'] = $builder->orderBy('view_count', 'DESC')->findAll();
            }

            // Search users (only for admin/superadmin)
            if (($type === 'users' || $type === 'all') && in_array(session()->get('role'), ['admin', 'superadmin'])) {
                $results['users'] = $this->userModel->like('full_name', $q)->orLike('email', $q)->findAll();
            }

            // Store history
            $userId = session()->get('user_id') ?? null;
            try {
                $filterData = compact('dateFrom', 'dateTo', 'appId', 'catId', 'status', 'priority');
                $this->historyModel->insert([
                    'user_id' => $userId,
                    'query' => $q,
                    'filters' => json_encode(array_filter($filterData)),
                    'results_count' => count($results['complaints']) + count($results['kb']) + count($results['users']),
                    'ip_address' => $this->request->getIPAddress(),
                ]);
            } catch (\Exception $e) {
                // fail silently
            }
        }

        return view('search/results', [
            'title' => 'Search Results',
            'q' => $q,
            'type' => $type,
            'results' => $results,
            'applications' => $applications,
            'categories' => $categories,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'appId' => $appId,
            'catId' => $catId,
            'status' => $status,
            'priority' => $priority,
        ]);
    }

    // AJAX suggestions: return small set of suggestions from KB and complaints and users
    public function suggestions()
    {
        $q = trim((string) $this->request->getGet('q'));
        $out = ['kb' => [], 'complaints' => [], 'users' => []];

        if ($q === '') return $this->response->setJSON($out);

        // KB suggestions (title)
        $kbRows = $this->kbModel->like('title', $q)->where('is_published', 1)->select('id,title')->orderBy('view_count', 'DESC')->limit(5)->findAll();
        foreach ($kbRows as $r) {
            $out['kb'][] = ['id' => $r->id, 'title' => $r->title, 'url' => base_url('knowledge-base/' . $r->id)];
        }

        // Complaint suggestions (title)
        $cRows = $this->complaintModel->like('title', $q)->select('id,title')->orderBy('created_at', 'DESC')->limit(5)->findAll();
        $currentRole = session()->get('role');
        foreach ($cRows as $r) {
            if ($currentRole === 'admin' || $currentRole === 'superadmin') {
                $url = base_url('admin/complaints/' . $r->id);
            } elseif ($currentRole === 'user') {
                $url = base_url('user/complaints/' . $r->id);
            } else {
                // Guest: point to login so they can access details after auth
                $url = base_url('auth/login') . '?redirect=' . urlencode('user/complaints/' . $r->id);
            }
            $out['complaints'][] = ['id' => $r->id, 'title' => $r->title, 'url' => $url];
        }

        // User suggestions
        // Only include user links for admin/superadmin
        if (in_array($currentRole, ['admin', 'superadmin'])) {
            $uRows = $this->userModel->like('full_name', $q)->orLike('email', $q)->select('id,full_name,email')->limit(5)->findAll();
            foreach ($uRows as $r) {
                $out['users'][] = ['id' => $r->id, 'name' => $r->full_name, 'email' => $r->email, 'url' => base_url('superadmin/users/' . $r->id . '/edit')];
            }
        }

        return $this->response->setJSON($out);
    }

    // Show recent search history for current user (requires auth)
    public function history()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([]);
        }
        $userId = session()->get('user_id');
        $rows = [];
        try {
            $rows = $this->historyModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->limit(20)->findAll();
        } catch (\Exception $e) {
            // silent
        }
        return $this->response->setJSON($rows);
    }

    // HTML view for paginated search history (for admins/users)
    public function historyPage()
    {
        // require auth
        if (! session()->has('user_id')) {
            return redirect()->to(route_to('login'));
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $q = trim((string) $this->request->getGet('q'));
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        $model = $this->historyModel;

        if ($q !== '') {
            $model = $model->like('query', $q);
        }
        if ($from) {
            $model = $model->where('created_at >=', $from . ' 00:00:00');
        }
        if ($to) {
            $model = $model->where('created_at <=', $to . ' 23:59:59');
        }

        $perPage = 20;
        try {
            $dataRows = $model->orderBy('created_at', 'DESC')->paginate($perPage);
            $pager = $model->pager;
        } catch (\Exception $e) {
            $dataRows = [];
            $pager = null;
        }

        // gather user names for display
        $userModel = new \App\Models\UserModel();
        $userIds = array_unique(array_filter(array_map(function ($r) {
            return $r['user_id'] ?? null;
        }, $dataRows)));
        $users = [];
        if (! empty($userIds)) {
            $userRows = $userModel->whereIn('id', $userIds)->select('id,full_name')->findAll();
            foreach ($userRows as $u) $users[$u->id] = $u->full_name;
        }

        return view('search/history', [
            'title' => 'Search History',
            'rows' => $dataRows,
            'pager' => $pager,
            'q' => $q,
            'from' => $from,
            'to' => $to,
            'users' => $users,
        ]);
    }
}
