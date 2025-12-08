<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints') ?>">Daftar Laporan</a></li>
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
                        <div class="d-flex gap-2">
                            <?= $complaint->getPriorityBadge() ?>
                            <?= $complaint->getStatusBadge() ?>
                        </div>
                    </div>
                    <?php if ($complaint->isPending()): ?>
                        <div class="btn-group">
                            <a href="<?= base_url('user/complaints/' . $complaint->id . '/edit') ?>"
                                class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="confirmDelete(<?= $complaint->id ?>)">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
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
                    <a href="<?= base_url('user/complaints/' . $complaint->id . '/chat') ?>"
                        class="btn btn-primary">
                        <i class="fas fa-comment-dots"></i> Buka Chat Internal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Info -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Informasi Status
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status Saat Ini:</small><br>
                    <?= $complaint->getStatusBadge() ?>
                </div>

                <?php if ($admin): ?>
                    <div class="mb-3">
                        <small class="text-muted">Ditangani Oleh:</small><br>
                        <strong><?= esc($admin->full_name) ?></strong>
                    </div>
                <?php endif; ?>

                <?php if ($complaint->resolved_at): ?>
                    <div class="mb-3">
                        <small class="text-muted">Diselesaikan:</small><br>
                        <strong><?= date('d M Y, H:i', strtotime($complaint->resolved_at)) ?></strong>
                    </div>
                <?php endif; ?>

                <?php if ($complaint->isResolved() && !$complaint->isClosed()): ?>
                    <hr>
                    <div class="d-grid">
                        <a href="<?= base_url('user/complaints/' . $complaint->id . '/feedback') ?>"
                            class="btn btn-success">
                            <i class="fas fa-star"></i> Beri Feedback
                        </a>
                        <small class="text-muted text-center mt-2">
                            Berikan penilaian Anda tentang penanganan laporan ini
                        </small>
                    </div>
                <?php elseif ($complaint->isClosed()): ?>
                    <?php
                    // Get feedback
                    $feedbackModel = new \App\Models\FeedbackModel();
                    $feedback = $feedbackModel->getFeedbackByComplaint($complaint->id);
                    ?>
                    <?php if ($feedback): ?>
                        <hr>
                        <div class="alert alert-success mb-0">
                            <strong><i class="fas fa-check-circle"></i> Feedback Terkirim</strong><br>
                            <div class="mt-2">
                                <?= $feedback->getRatingStars() ?>
                                <?php if ($feedback->comment): ?>
                                    <p class="mb-0 mt-2 small">
                                        <em>"<?= esc($feedback->comment) ?>"</em>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
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
                            <?php
                            $action = $h['action'] ?? null;
                            $createdAt = $h['created_at'] ?? null;
                            // Relative time
                            $timeDiff = 'Baru saja';
                            if ($createdAt) {
                                $created = new DateTime($createdAt);
                                $now = new DateTime();
                                $diff = $now->diff($created);
                                if ($diff->days > 0) {
                                    $timeDiff = $diff->days . ' hari yang lalu';
                                } elseif ($diff->h > 0) {
                                    $timeDiff = $diff->h . ' jam yang lalu';
                                } elseif ($diff->i > 0) {
                                    $timeDiff = $diff->i . ' menit yang lalu';
                                }
                            }
                            ?>
                            <div class="timeline-item mb-3" data-action="<?= esc($action) ?>">
                                <div class="d-flex">
                                    <div class="timeline-icon me-2">
                                        <div class="rounded-circle" style="width:28px;height:28px;background-color:var(--bs-<?= \App\Models\ComplaintHistoryModel::getActionBadgeClass($action) ?>);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;">
                                            <i class="fas <?= \App\Models\ComplaintHistoryModel::getActionIcon($action) ?>"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong><?= \App\Models\ComplaintHistoryModel::getActionLabel($action) ?></strong><br>
                                        <?php if (!empty($h['description'])): ?>
                                            <small class="text-muted"><?= esc($h['description']) ?></small><br>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <?= esc($h['user_name'] ?? 'System') ?> • <?= $timeDiff ?>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
                <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= base_url('user/complaints/') ?>' + id;

        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>