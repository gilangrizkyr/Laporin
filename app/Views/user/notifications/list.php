<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Notifikasi</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-bell"></i> Notifikasi
        </h5>
        <?php if (!empty($notifications)): ?>
            <button class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()" id="markAllBtn">
                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
            </button>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada notifikasi</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">

                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item list-group-item-action <?= !$notif->is_read ? 'bg-light' : '' ?>"
                        style="cursor: pointer; transition: all 0.3s;"
                        onclick="handleNotificationClick(
                            <?= $notif->id ?>,
                            <?= $notif->complaint_id ? $notif->complaint_id : 'null' ?>
                        )">

                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if (!$notif->is_read): ?>
                                        <span class="badge bg-primary me-2">Baru</span>
                                    <?php endif; ?>

                                    <h6 class="mb-0">
                                        <i class="fas fa-<?= $notif->complaint_id ? 'clipboard-list' : 'info-circle' ?>"></i>
                                        <?= esc($notif->title) ?>
                                    </h6>
                                </div>

                                <p class="mb-2 text-muted"><?= esc($notif->message) ?></p>

                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?= $notif->getTimeDiff() ?>
                                </small>
                            </div>

                            <div class="ms-3" onclick="event.stopPropagation();">
                                <div class="btn-group btn-group-sm">
                                    <?php if (!$notif->is_read): ?>
                                        <button class="btn btn-outline-primary"
                                            onclick="markAsRead(<?= $notif->id ?>)"
                                            title="Tandai Dibaca">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>

                                    <button class="btn btn-outline-danger"
                                        onclick="deleteNotif(<?= $notif->id ?>)"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

            <?php if (isset($pager) && $pager): ?>
                <div class="p-3">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    function handleNotificationClick(notifId, complaintId) {
        fetch('<?= base_url('user/notifications') ?>/' + notifId + '/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(() => {
            if (complaintId) {
                window.location.href = '<?= base_url('user/complaints') ?>/' + complaintId;
            } else {
                location.reload();
            }
        });
    }

    function markAsRead(id) {
        event.stopPropagation();
        fetch('<?= base_url('user/notifications') ?>/' + id + '/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(d => {
            if (d.success) location.reload();
        });
    }

    function markAllAsRead() {
        if (!confirm('Tandai semua notifikasi sebagai dibaca?')) return;

        fetch('<?= base_url('user/notifications/read-all') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(d => {
            if (d.success) location.reload();
        });
    }

    function deleteNotif(id) {
        event.stopPropagation();
        if (!confirm('Hapus notifikasi ini?')) return;

        fetch('<?= base_url('user/notifications') ?>/' + id, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(d => {
            if (d.success) location.reload();
        });
    }
</script>
<?= $this->endSection() ?>
