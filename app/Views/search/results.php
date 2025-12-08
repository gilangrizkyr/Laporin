<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Advanced Search</h5>
    </div>
    <div class="card-body">
        <form method="get" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" value="<?= esc($q) ?>" class="form-control form-control-sm" placeholder="Keyword...">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="all" <?= ($type == 'all' || !$type) ? 'selected' : '' ?>>All Types</option>
                    <option value="complaints" <?= $type == 'complaints' ? 'selected' : '' ?>>Complaints</option>
                    <option value="kb" <?= $type == 'kb' ? 'selected' : '' ?>>KB</option>
                    <option value="users" <?= $type == 'users' ? 'selected' : '' ?>>Users</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" value="<?= esc($dateFrom) ?>" class="form-control form-control-sm" title="From date">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" value="<?= esc($dateTo) ?>" class="form-control form-control-sm" title="To date">
            </div>
            <div class="col-md-2">
                <select name="app_id" class="form-select form-select-sm">
                    <option value="">All Apps</option>
                    <?php foreach ($applications as $app): ?>
                        <option value="<?= $app->id ?>" <?= $appId == $app->id ? 'selected' : '' ?>><?= esc($app->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>

        <!-- Additional filters on second row -->
        <div class="row g-2 mt-2">
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat->id ?>" <?= $catId == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $status == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $status == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="closed" <?= $status == 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priority</option>
                    <option value="normal" <?= $priority == 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="important" <?= $priority == 'important' ? 'selected' : '' ?>>Important</option>
                    <option value="urgent" <?= $priority == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                </select>
            </div>
            <div class="col-md-3">
                <a href="<?= base_url('search') ?>" class="btn btn-secondary btn-sm w-100">Reset</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Complaints Results -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list"></i> Complaints (<?= count($results['complaints']) ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($results['complaints'])): ?>
                    <div class="list-group">
                        <?php foreach ($results['complaints'] as $c): ?>
                            <a href="<?= base_url('admin/complaints/' . $c->id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($c->title) ?></h6>
                                    <span class="badge bg-<?= $c->status == 'pending' ? 'warning' : ($c->status == 'in_progress' ? 'info' : ($c->status == 'resolved' ? 'success' : 'secondary')) ?>">
                                        <?= ucfirst(str_replace('_', ' ', $c->status)) ?>
                                    </span>
                                </div>
                                <p class="mb-1 text-muted"><?= substr(esc($c->description), 0, 80) ?>...</p>
                                <small><?= date('Y-m-d', strtotime($c->created_at)) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No complaints found</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- KB Results -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-book"></i> Knowledge Base (<?= count($results['kb']) ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($results['kb'])): ?>
                    <div class="list-group">
                        <?php foreach ($results['kb'] as $kb): ?>
                            <a href="<?= base_url('knowledge-base/' . $kb->id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($kb->title) ?></h6>
                                    <span class="badge bg-light text-dark"><?= $kb->view_count ?> views</span>
                                </div>
                                <p class="mb-1 text-muted"><?= substr(strip_tags($kb->content), 0, 80) ?>...</p>
                                <small><?= date('Y-m-d', strtotime($kb->created_at)) ?></small>
                            </a>

                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No articles found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Users Results (admin only) -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-users"></i> Users (<?= count($results['users']) ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($results['users'])): ?>
                    <div class="list-group">
                        <?php foreach ($results['users'] as $u): ?>
                            <a href="<?= base_url('superadmin/users/' . $u->id . '/edit') ?>" class="list-group-item list-group-item-action">
                                <h6 class="mb-1"><?= esc($u->full_name) ?></h6>
                                <small class="text-muted"><?= esc($u->email) ?></small>
                            </a>
                        <?php endforeach; ?>

                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No users found</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Search History</h6>
            </div>
            <div class="card-body">
                <a href="<?= base_url('search/history/view') ?>" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fas fa-history"></i> View History
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>