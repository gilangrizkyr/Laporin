<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Dashboard</h2>
        <p class="text-muted mb-0">Selamat datang, <?= esc(session()->get('full_name')) ?>!</p>
    </div>
    <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success btn-lg">
        <i class="fas fa-plus-circle"></i> Buat Laporan Baru
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-primary text-black">
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
        <div class="stat-card bg-warning text-black">
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
        <div class="stat-card bg-info text-black">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?= number_format($stats['in_progress']) ?></div>
                    <div class="stat-label">Diproses</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card bg-success text-black">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?= number_format($stats['resolved']) ?></div>
                    <div class="stat-label">Selesai</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
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
        <a href="<?= base_url('user/complaints') ?>" class="btn btn-sm btn-outline-success">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($recentComplaints)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($recentComplaints as $complaint): ?>
                            <tr>
                                <td><strong><?= $no++ ?></strong></td>

                                <td>
                                    <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" class="text-decoration-none">
                                        <?= esc($complaint->title) ?>
                                    </a>
                                </td>

                                <td><?= $complaint->getPriorityBadge() ?></td>
                                <td><?= $complaint->getStatusBadge() ?></td>

                                <td>
                                    <small class="text-muted">
                                        <?= date('d M Y', strtotime($complaint->created_at)) ?>
                                    </small>
                                </td>

                                <td>
                                    <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" class="btn btn-sm btn-outline-primary">
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
                <h5 class="text-muted">Belum Ada Laporan</h5>
                <p class="text-muted">Anda belum membuat laporan pengaduan</p>
                <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Buat Laporan Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card h-100 border-success">
            <div class="card-body text-center">
                <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                <h5>Buat Laporan</h5>
                <p class="text-muted">Laporkan kendala atau bug aplikasi</p>
                <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success">
                    Buat Sekarang
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card h-100 border-info">
            <div class="card-body text-center">
                <i class="fas fa-list fa-3x text-info mb-3"></i>
                <h5>Lihat Laporan</h5>
                <p class="text-muted">Pantau status pengaduan Anda</p>
                <a href="<?= base_url('user/complaints') ?>" class="btn btn-info">
                    Lihat Semua
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card h-100 border-primary">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5>Knowledge Base</h5>
                <p class="text-muted">Cari solusi masalah umum</p>
                <a href="<?= base_url('knowledge-base') ?>" class="btn btn-primary">
                    Buka KB
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>