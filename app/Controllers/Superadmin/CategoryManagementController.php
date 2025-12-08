<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryManagementController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        return view('superadmin/categories/index', ['title' => 'Categories', 'page_title' => 'Category Management', 'categories' => $categories]);
    }

    public function create()
    {
        return view('superadmin/categories/form', ['title' => 'Create Category', 'page_title' => 'Create Category']);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        $insertId = $this->categoryModel->insert($data);
        if ($insertId === false) {
            $errors = $this->categoryModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('superadmin/categories'))->with('success', 'Category created');
    }

    public function edit($id)
    {
        $cat = $this->categoryModel->find($id);
        if (!$cat) return redirect()->to(base_url('superadmin/categories'))->with('error', 'Category not found');
        return view('superadmin/categories/form', ['title' => 'Edit Category', 'page_title' => 'Edit Category', 'category' => $cat]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        $updated = $this->categoryModel->update($id, $data);
        if ($updated === false) {
            $errors = $this->categoryModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to(base_url('superadmin/categories'))->with('success', 'Category updated');
    }

    public function delete($id)
    {
        if ($this->categoryModel->delete($id)) {
            return redirect()->to(base_url('superadmin/categories'))->with('success', 'Category deleted');
        }
        return redirect()->back()->with('error', 'Failed to delete category');
    }
}
