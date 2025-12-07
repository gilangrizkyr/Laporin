<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Daftar Laporan</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Daftar Laporan Saya</h2>
        <p class="text-muted mb-0">Kelola semua laporan pengaduan Anda</p>
    </div>
    <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success">
        <i class="fas fa-plus-circle"></i> Buat Laporan Baru
    </a>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('user/complaints') ?>">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= $currentStatus == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= $currentStatus == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="resolved" <?= $currentStatus == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= $currentStatus == 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Prioritas</label>
                    <select name="priority" class="form-select">
                        <option value="">Semua Prioritas</option>
                        <option value="normal" <?= $currentPriority == 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="important" <?= $currentPriority == 'important' ? 'selected' : '' ?>>Important</option>
                        <option value="urgent" <?= $currentPriority == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Complaints List -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($complaints)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Judul</th>
                            <th style="width: 120px;">Prioritas</th>
                            <th style="width: 130px;">Status</th>
                            <th style="width: 150px;">Tanggal</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td>
                                    <strong>#<?= $complaint->id ?></strong>
                                </td>
                                <td>
                                    <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" class="text-decoration-none fw-bold">
                                        <?= esc($complaint->title) ?>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <?= esc(substr($complaint->description, 0, 60)) ?>...
                                    </small>
                                </td>
                                <td><?= $complaint->getPriorityBadge() ?></td>
                                <td><?= $complaint->getStatusBadge() ?></td>
                                <td>
                                    <small>
                                        <?= date('d M Y', strtotime($complaint->created_at)) ?><br>
                                        <span class="text-muted"><?= $complaint->getCreatedDiff() ?></span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" 
                                           class="btn btn-outline-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($complaint->isPending()): ?>
                                            <a href="<?= base_url('user/complaints/' . $complaint->id . '/edit') ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
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
                <p class="text-muted mb-3">
                    <?php if ($currentStatus || $currentPriority): ?>
                        Tidak ada laporan dengan filter yang dipilih
                    <?php else: ?>
                        Anda belum membuat laporan pengaduan
                    <?php endif; ?>
                </p>
                <?php if ($currentStatus || $currentPriority): ?>
                    <a href="<?= base_url('user/complaints') ?>" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset Filter
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Buat Laporan Pertama
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>