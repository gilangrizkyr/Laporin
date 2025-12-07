<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/complaints') ?>">Kelola Laporan</a></li>
        <li class="breadcrumb-item active">Detail #<?= $complaint->id ?></li>
    </ol>
</nav>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Complaint Info Card -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-2">
                            <span class="badge bg-secondary">#<?= $complaint->id ?></span>
                            <?= esc($complaint->title) ?>
                        </h5>
                        <div class="d-flex gap-2 mb-2">
                            <?= $complaint->getPriorityBadge() ?>
                            <?= $complaint->getStatusBadge() ?>
                        </div>
                        <small class="text-muted">
                            Pelapor: <strong><?= esc($complaint->user_name) ?></strong> (<?= esc($complaint->user_email) ?>)
                        </small>
                    </div>
                    <?php if (!$complaint->assigned_to): ?>
                        <form action="<?= base_url('admin/complaints/' . $complaint->id . '/assign') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-check"></i> Assign ke Saya
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Meta Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Aplikasi:</small><br>
                        <strong><?= esc($complaint->application_name) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Kategori:</small><br>
                        <strong><?= esc($complaint->category_name ?? '-') ?></strong>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Dampak:</small><br>
                        <span class="badge bg-secondary"><?= $complaint->getImpactLabel() ?></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Dilaporkan:</small><br>
                        <strong><?= date('d M Y, H:i', strtotime($complaint->created_at)) ?></strong>
                    </div>
                </div>

                <hr>

                <!-- Description -->
                <h6 class="mb-2">Deskripsi:</h6>
                <p style="white-space: pre-line;"><?= esc($complaint->description) ?></p>

                <!-- Attachments -->
                <?php if (!empty($attachments)): ?>
                    <hr>
                    <h6 class="mb-3">Lampiran (<?= count($attachments) ?>):</h6>
                    <div class="row">
                        <?php foreach ($attachments as $attachment): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <?php if ($attachment->isImage()): ?>
                                            <a href="<?= $attachment->getFileUrl() ?>" target="_blank">
                                                <img src="<?= $attachment->getFileUrl() ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="<?= esc($attachment->file_name) ?>"
                                                     style="max-height: 150px;">
                                            </a>
                                        <?php else: ?>
                                            <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                        <?php endif; ?>
                                        <p class="mb-1 small">
                                            <strong><?= esc($attachment->file_name) ?></strong>
                                        </p>
                                        <small class="text-muted"><?= $attachment->getFileSizeFormatted() ?></small>
                                        <br>
                                        <a href="<?= $attachment->getFileUrl() ?>" 
                                           class="btn btn-sm btn-outline-primary mt-2" 
                                           target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Feedback if exists -->
                <?php if ($feedback): ?>
                    <hr>
                    <div class="alert alert-success">
                        <h6><i class="fas fa-star"></i> Feedback dari User</h6>
                        <div class="mt-2">
                            <?= $feedback->getRatingStars() ?>
                            <?php if ($feedback->comment): ?>
                                <p class="mb-0 mt-2">
                                    <em>"<?= esc($feedback->comment) ?>"</em>
                                </p>
                            <?php endif; ?>
                            <small class="text-muted">
                                Diberikan pada: <?= date('d M Y, H:i', strtotime($feedback->created_at)) ?>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Section -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-comments"></i> Komunikasi
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <a href="<?= base_url('admin/complaints/' . $complaint->id . '/chat') ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-comment-dots"></i> Buka Chat Internal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Action Buttons -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-cog"></i> Aksi
                </h6>
            </div>
            <div class="card-body">
                <!-- Change Status -->
                <div class="mb-3">
                    <label class="form-label"><strong>Ubah Status:</strong></label>
                    <select class="form-select" id="statusSelect" onchange="changeStatus(<?= $complaint->id ?>)">
                        <option value="pending" <?= $complaint->status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= $complaint->status == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="resolved" <?= $complaint->status == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= $complaint->status == 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>

                <!-- Change Priority -->
                <div class="mb-3">
                    <label class="form-label"><strong>Ubah Prioritas:</strong></label>
                    <select class="form-select" id="prioritySelect" onchange="changePriority(<?= $complaint->id ?>)">
                        <option value="normal" <?= $complaint->priority == 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="important" <?= $complaint->priority == 'important' ? 'selected' : '' ?>>Important</option>
                        <option value="urgent" <?= $complaint->priority == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>

                <hr>

                <!-- Quick Actions -->
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/complaints/' . $complaint->id . '/chat') ?>" 
                       class="btn btn-info">
                        <i class="fas fa-comment"></i> Chat dengan User
                    </a>
                    <button class="btn btn-outline-secondary" onclick="alert('Export PDF coming soon')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Informasi
                </h6>
            </div>
            <div class="card-body">
                <?php if ($admin): ?>
                    <div class="mb-3">
                        <small class="text-muted">Ditangani Oleh:</small><br>
                        <strong><?= esc($admin->full_name) ?></strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <small>Laporan belum di-assign</small>
                    </div>
                <?php endif; ?>

                <?php if ($complaint->resolved_at): ?>
                    <div class="mb-3">
                        <small class="text-muted">Diselesaikan:</small><br>
                        <strong><?= date('d M Y, H:i', strtotime($complaint->resolved_at)) ?></strong>
                    </div>
                <?php endif; ?>

                <?php if ($complaint->closed_at): ?>
                    <div class="mb-3">
                        <small class="text-muted">Ditutup:</small><br>
                        <strong><?= date('d M Y, H:i', strtotime($complaint->closed_at)) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- History Timeline -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-history"></i> Riwayat Aktivitas
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($history)): ?>
                    <div class="timeline">
                        <?php foreach ($history as $h): ?>
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="timeline-icon me-2">
                                        <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong><?= $h->getActionLabel() ?></strong>
                                        <?php if ($h->role == 'admin' || $h->role == 'superadmin'): ?>
                                            <span class="badge bg-info">Admin</span>
                                        <?php endif; ?>
                                        <br>
                                        <?php if ($h->description): ?>
                                            <small class="text-muted"><?= esc($h->description) ?></small><br>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <?= esc($h->user_name) ?> • <?= $h->getTimeDiff() ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Belum ada aktivitas</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function changeStatus(complaintId) {
    const status = document.getElementById('statusSelect').value;
    
    if (!confirm('Ubah status menjadi ' + status + '?')) {
        location.reload();
        return;
    }

    fetch(`<?= base_url('admin/complaints/') ?>${complaintId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `status=${status}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function changePriority(complaintId) {
    const priority = document.getElementById('prioritySelect').value;
    
    if (!confirm('Ubah prioritas menjadi ' + priority + '?')) {
        location.reload();
        return;
    }

    fetch(`<?= base_url('admin/complaints/') ?>${complaintId}/priority`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `priority=${priority}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
<?= $this->endSection() ?>