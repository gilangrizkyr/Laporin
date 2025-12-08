<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Notifikasi</h5>
        <button class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()" id="markAllBtn">Mark All as Read</button>
    </div>
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <p class="text-muted text-center">Tidak ada notifikasi</p>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <?= esc($notif['title']) ?>
                                    <?php if (!$notif['is_read']): ?>
                                        <span class="badge bg-primary">New</span>
                                    <?php endif; ?>
                                </h6>
                                <p class="mb-1 text-muted"><?= esc($notif['message']) ?></p>
                                <small class="text-muted"><?= date('Y-m-d H:i', strtotime($notif['created_at'])) ?></small>
                            </div>
                            <div class="ms-2">
                                <?php if (!$notif['is_read']): ?>
                                    <button class="btn btn-sm btn-light" onclick="markAsRead(<?= $notif['id'] ?>)">Mark as read</button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteNotif(<?= $notif['id'] ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($pager) && $pager): ?>
                <div class="mt-3">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function markAsRead(id) {
    fetch('<?= base_url('admin/notifications') ?>/' + id + '/read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        }
    })
    .catch(e => console.error(e));
}

function markAllAsRead() {
    fetch('<?= base_url('admin/notifications/read-all') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        }
    })
    .catch(e => console.error(e));
}

function deleteNotif(id) {
    if (!confirm('Delete this notification?')) return;
    fetch('<?= base_url('admin/notifications') ?>/' + id, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        }
    })
    .catch(e => console.error(e));
}
</script>
<?= $this->endSection() ?>
