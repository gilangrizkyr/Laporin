<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Dashboard Admin</h2>
        <p class="text-muted mb-0">Selamat datang, <?= esc(session()->get('full_name')) ?>!</p>
    </div>
</div>

<!-- Global Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?= number_format($stats['total']) ?></div>
                    <div class="stat-label">Total Laporan</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?= number_format($stats['pending']) ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?= number_format($stats['in_progress']) ?></div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?= number_format($stats['urgent']) ?></div>
                    <div class="stat-label">Urgent</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">
                    <i class="fas fa-inbox"></i> Belum Ditangani
                </h5>
                <h2 class="mb-3"><?= count($unassignedComplaints) ?></h2>
                <a href="<?= base_url('admin/complaints?status=pending&assigned=unassigned') ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-eye"></i> Lihat Semua
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body">
                <h5 class="card-title text-info">
                    <i class="fas fa-user-check"></i> Laporan Saya
                </h5>
                <h2 class="mb-3"><?= count($myComplaints) ?></h2>
                <a href="<?= base_url('admin/complaints?assigned_to=' . session()->get('user_id')) ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Lihat Semua
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">
                    <i class="fas fa-fire"></i> Urgent
                </h5>
                <h2 class="mb-3"><?= count($urgentComplaints) ?></h2>
                <a href="<?= base_url('admin/complaints?priority=urgent') ?>" class="btn btn-danger btn-sm">
                    <i class="fas fa-eye"></i> Lihat Semua
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Complaints -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-history"></i> Laporan Terbaru
        </h5>
        <a href="<?= base_url('admin/complaints') ?>" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($recentComplaints)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentComplaints as $complaint): ?>
                            <tr>
                                <td><strong>#<?= $complaint->id ?></strong></td>
                                <td>
                                    <a href="<?= base_url('admin/complaints/' . $complaint->id) ?>" class="text-decoration-none">
                                        <?= esc($complaint->title) ?>
                                    </a>
                                    <br>
                                    <small class="text-muted"><?= esc($complaint->application_name) ?></small>
                                </td>
                                <td><?= esc($complaint->user_name) ?></td>
                                <td><?= $complaint->getPriorityBadge() ?></td>
                                <td><?= $complaint->getStatusBadge() ?></td>
                                <td>
                                    <small><?= date('d M Y', strtotime($complaint->created_at)) ?></small>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/complaints/' . $complaint->id) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada laporan</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>