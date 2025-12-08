<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <div>
        <h3>Knowledge Base</h3>
        <p class="text-muted mb-0">Manage help articles and documentation</p>
    </div>
    <div class="gap-2" style="display: flex; gap: 0.5rem;">
        <a href="<?= base_url('admin/knowledge-base/analytics') ?>" class="btn btn-outline-info btn-sm">Analytics</a>
        <a href="<?= base_url('admin/knowledge-base/create') ?>" class="btn btn-primary btn-sm">Create Article</a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Title</th>
                    <th style="width: 80px">Status</th>
                    <th style="width: 80px">Views</th>
                    <th style="width: 150px">Created</th>
                    <th style="width: 150px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= $article->id ?></td>
                        <td><?= esc($article->title) ?></td>
                        <td>
                            <?php if ($article->is_published): ?>
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$article->view_count ?></td>
                        <td><?= date('Y-m-d', strtotime($article->created_at)) ?></td>
                        <td>
                            <a href="<?= base_url('admin/knowledge-base/' . $article->id . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?= base_url('admin/knowledge-base/' . $article->id) ?>" method="post" style="display:inline-block" onsubmit="return confirm('Delete article?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($articles)): ?>
            <div class="text-center text-muted py-4">
                <p>No articles yet. <a href="<?= base_url('admin/knowledge-base/create') ?>">Create one</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
