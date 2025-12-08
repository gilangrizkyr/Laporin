<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-warning">
        <h5 class="mb-0">Override Priority - Complaint #<?= $complaint->id ?></h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <strong>Current Details:</strong>
            <ul class="mb-0">
                <li>Application: <strong><?= esc($complaint->application_id) ?></strong></li>
                <li>Current Priority: <strong><?= ucfirst($complaint->priority) ?></strong></li>
                <li>Status: <strong><?= ucfirst($complaint->status) ?></strong></li>
                <li>Created: <strong><?= date('Y-m-d H:i:s', strtotime($complaint->created_at)) ?></strong></li>
            </ul>
        </div>

        <form method="post" action="<?= base_url('superadmin/complaints/' . $complaint->id . '/override-priority') ?>">
            <div class="mb-3">
                <label class="form-label">New Priority <span class="text-danger">*</span></label>
                <select name="priority" class="form-select" required>
                    <option value="">-- Select Priority --</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason for Override <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Explain why you're overriding the priority..." required></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning">Override Priority</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>