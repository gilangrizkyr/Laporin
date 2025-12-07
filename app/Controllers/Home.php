<?php

namespace App\Controllers;

use App\Models\ComplaintModel;
use App\Models\KnowledgeBaseModel;

class Home extends BaseController
{
    protected $complaintModel;
    protected $kbModel;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->kbModel = new KnowledgeBaseModel();
    }

    /**
     * Landing Page - Halaman pertama saat akses website
     */
    public function index()
    {
        // Get public statistics
        $stats = $this->getPublicStatistics();
        
        // Get recent public complaints (tanpa info sensitif)
        $recentComplaints = $this->getRecentPublicComplaints(5);
        
        // Get popular KB articles
        $popularArticles = $this->kbModel->getPopularArticles(3);

        $data = [
            'title' => 'Sistem Pengaduan Aplikasi Internal',
            'stats' => $stats,
            'recentComplaints' => $recentComplaints,
            'popularArticles' => $popularArticles,
        ];

        return view('home/landing', $data);
    }

    /**
     * Knowledge Base - Halaman daftar artikel
     */
    public function knowledgeBase()
    {
        $articles = $this->kbModel->getPublishedArticles();

        $data = [
            'title' => 'Knowledge Base - Pusat Informasi',
            'articles' => $articles,
        ];

        return view('home/knowledge_base', $data);
    }

    /**
     * Knowledge Base Detail - Halaman detail artikel
     */
    public function knowledgeBaseDetail($id)
    {
        $article = $this->kbModel->find($id);

        if (!$article || !$article->isPublished()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment view count
        $this->kbModel->incrementViewCount($id);

        // Get related articles
        $relatedArticles = $this->kbModel
            ->where('is_published', 1)
            ->where('id !=', $id)
            ->limit(3)
            ->findAll();

        $data = [
            'title' => $article->title,
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ];

        return view('home/knowledge_base_detail', $data);
    }

    /**
     * Knowledge Base Search
     */
    public function knowledgeBaseSearch()
    {
        $keyword = $this->request->getGet('q');
        $articles = [];

        if ($keyword) {
            $articles = $this->kbModel->searchArticles($keyword);
        }

        $data = [
            'title' => 'Pencarian Knowledge Base',
            'keyword' => $keyword,
            'articles' => $articles,
        ];

        return view('home/knowledge_base_search', $data);
    }

    /**
     * Get public statistics (tanpa info sensitif)
     */
    protected function getPublicStatistics(): array
    {
        $stats = $this->complaintModel->getGlobalStats();
        
        return [
            'total' => $stats['total'],
            'in_progress' => $stats['in_progress'],
            'resolved' => $stats['resolved'],
        ];
    }

    /**
     * Get recent complaints untuk publik
     * Hanya menampilkan: ID, judul (partial), status, prioritas, tanggal
     * TIDAK menampilkan: nama pelapor, deskripsi lengkap, kontak
     */
    protected function getRecentPublicComplaints(int $limit = 5): array
    {
        $complaints = $this->complaintModel
            ->select('complaints.id, complaints.title, complaints.priority, complaints.status, complaints.created_at, applications.name as application_name')
            ->join('applications', 'applications.id = complaints.application_id')
            ->orderBy('complaints.created_at', 'DESC')
            ->limit($limit)
            ->findAll();

        // Anonymize data
        foreach ($complaints as $complaint) {
            // Truncate title jika terlalu panjang
            if (strlen($complaint->title) > 50) {
                $complaint->title = substr($complaint->title, 0, 50) . '...';
            }
        }

        return $complaints;
    }
}
