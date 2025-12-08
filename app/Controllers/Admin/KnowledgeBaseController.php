<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KnowledgeBaseModel;
use App\Models\ApplicationModel;
use App\Models\CategoryModel;

class KnowledgeBaseController extends BaseController
{
    protected $kbModel;
    protected $appModel;
    protected $catModel;

    public function __construct()
    {
        $this->kbModel = new KnowledgeBaseModel();
        $this->appModel = new ApplicationModel();
        $this->catModel = new CategoryModel();
    }

    public function index()
    {
        $articles = $this->kbModel->orderBy('created_at', 'DESC')->findAll();
        return view('admin/knowledge_base/index', [
            'title' => 'Knowledge Base',
            'page_title' => 'Knowledge Base Management',
            'articles' => $articles
        ]);
    }

    public function create()
    {
        $apps = $this->appModel->findAll();
        $categories = $this->catModel->findAll();
        return view('admin/knowledge_base/form', [
            'title' => 'Create Article',
            'page_title' => 'Create Knowledge Base Article',
            'apps' => $apps,
            'categories' => $categories
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['is_published'] = isset($data['is_published']) ? 1 : 0;
        $data['created_by'] = session()->get('user_id');

        $insertId = $this->kbModel->insert($data);
        if ($insertId === false) {
            $errors = $this->kbModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('admin/knowledge-base'))->with('success', 'Article created');
    }

    public function edit($id)
    {
        $article = $this->kbModel->find($id);
        if (!$article) {
            return redirect()->to(base_url('admin/knowledge-base'))->with('error', 'Article not found');
        }

        $apps = $this->appModel->findAll();
        $categories = $this->catModel->findAll();
        return view('admin/knowledge_base/form', [
            'title' => 'Edit Article',
            'page_title' => 'Edit Knowledge Base Article',
            'article' => $article,
            'apps' => $apps,
            'categories' => $categories
        ]);
    }

    public function update($id)
    {
        $article = $this->kbModel->find($id);
        if (!$article) {
            return redirect()->to(base_url('admin/knowledge-base'))->with('error', 'Article not found');
        }

        $data = $this->request->getPost();
        $data['is_published'] = isset($data['is_published']) ? 1 : 0;

        $updated = $this->kbModel->update($id, $data);
        if ($updated === false) {
            $errors = $this->kbModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('admin/knowledge-base'))->with('success', 'Article updated');
    }

    public function delete($id)
    {
        if ($this->kbModel->delete($id)) {
            return redirect()->to(base_url('admin/knowledge-base'))->with('success', 'Article deleted');
        }
        return redirect()->back()->with('error', 'Failed to delete article');
    }

    public function analytics()
    {
        $articles = $this->kbModel->orderBy('view_count', 'DESC')->findAll();

        $stats = [
            'total_articles' => $this->kbModel->countAllResults(),
            'published' => $this->kbModel->where('is_published', 1)->countAllResults(false),
            'drafts' => $this->kbModel->where('is_published', 0)->countAllResults(false),
            'total_views' => 0,
        ];

        // Calculate total views
        $viewResult = $this->kbModel->selectSum('view_count')->get()->getRow();
        $stats['total_views'] = $viewResult ? (int)$viewResult->view_count : 0;

        return view('admin/knowledge_base/analytics', [
            'title' => 'KB Analytics',
            'page_title' => 'Knowledge Base Analytics',
            'stats' => $stats,
            'articles' => $articles
        ]);
    }
}
