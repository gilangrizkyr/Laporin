<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Knowledge Base Analytics</h3>
        <p class="text-muted mb-0">View statistics and popular articles</p>
    </div>
    <a href="<?= base_url('admin/knowledge-base') ?>" class="btn btn-outline-secondary btn-sm">Back to Articles</a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Articles</h6>
                <h3 class="text-primary"><?= $stats['total_articles'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Published</h6>
                <h3 class="text-success"><?= $stats['published'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Drafts</h6>
                <h3 class="text-warning"><?= $stats['drafts'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Views</h6>
                <h3 class="text-info"><?= $stats['total_views'] ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Popular Articles -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Most Popular Articles</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Title</th>
                    <th style="width: 80px">Status</th>
                    <th style="width: 100px">Views</th>
                    <th style="width: 150px">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= $article->id ?></td>
                        <td>
                            <strong><?= esc($article->title) ?></strong>
                            <?php if (!empty($article->tags)): ?>
                                <br><small class="text-muted"><?= esc($article->tags) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($article->is_published): ?>
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= (int)$article->view_count ?></strong>
                            <?php if ($stats['total_views'] > 0): ?>
                                <small class="text-muted d-block"><?= round(($article->view_count / $stats['total_views']) * 100, 1) ?>%</small>
                            <?php endif; ?>
                        </td>
                        <td><?= date('Y-m-d', strtotime($article->created_at)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($articles)): ?>
            <div class="text-center text-muted py-4">
                <p>No articles yet</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
