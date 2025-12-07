<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Laporan</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Kelola Semua Laporan</h2>
        <p class="text-muted mb-0">Manage dan handle semua pengaduan yang masuk</p>
    </div>
</div>

<!-- Advanced Filter -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-filter"></i> Filter Laporan
        </h6>
    </div>
    <div class="card-body">
        <form method="get" action="<?= base_url('admin/complaints') ?>">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= $filters['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= $filters['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="resolved" <?= $filters['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= $filters['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Prioritas</label>
                    <select name="priority" class="form-select">
                        <option value="">Semua Prioritas</option>
                        <option value="urgent" <?= $filters['priority'] == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        <option value="important" <?= $filters['priority'] == 'important' ? 'selected' : '' ?>>Important</option>
                        <option value="normal" <?= $filters['priority'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Aplikasi</label>
                    <select name="application_id" class="form-select">
                        <option value="">Semua Aplikasi</option>
                        <?php foreach ($applications as $app): ?>
                            <option value="<?= $app->id ?>" <?= $filters['application_id'] == $app->id ? 'selected' : '' ?>>
                                <?= esc($app->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Ditangani Oleh</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Semua Admin</option>
                        <?php foreach ($admins as $admin): ?>
                            <option value="<?= $admin->id ?>" <?= $filters['assigned_to'] == $admin->id ? 'selected' : '' ?>>
                                <?= esc($admin->full_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from']) ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to']) ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="<?= base_url('admin/complaints') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Complaints List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Daftar Laporan (<?= count($complaints) ?>)
        </h5>
        <button class="btn btn-sm btn-success" onclick="alert('Export feature coming soon')">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>
    <div class="card-body">
        <?php if (!empty($complaints)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th style="width: 120px;">Prioritas</th>
                            <th style="width: 130px;">Status</th>
                            <th>Admin</th>
                            <th style="width: 150px;">Tanggal</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td><strong>#<?= $complaint->id ?></strong></td>
                                <td>
                                    <a href="<?= base_url('admin/complaints/' . $complaint->id) ?>" class="text-decoration-none fw-bold">
                                        <?= esc($complaint->title) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $userModel = new \App\Models\UserModel();
                                    $user = $userModel->find($complaint->user_id);
                                    echo esc($user->full_name ?? 'Unknown');
                                    ?>
                                </td>
                                <td><?= $complaint->getPriorityBadge() ?></td>
                                <td><?= $complaint->getStatusBadge() ?></td>
                                <td>
                                    <?php if ($complaint->assigned_to): ?>
                                        <?php
                                        $admin = $userModel->find($complaint->assigned_to);
                                        echo esc($admin->full_name ?? '-');
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belum</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= date('d M Y', strtotime($complaint->created_at)) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/complaints/' . $complaint->id) ?>"
                                            class="btn btn-outline-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!$complaint->assigned_to): ?>
                                            <form action="<?= base_url('admin/complaints/' . $complaint->id . '/assign') ?>"
                                                method="post" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-success" title="Assign ke Saya">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Laporan</h5>
                <p class="text-muted">
                    <?php if (array_filter($filters) || $showUnassigned): ?>
                        Tidak ada laporan dengan filter yang dipilih
                    <?php else: ?>
                        Belum ada laporan yang masuk
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>