<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-eye"></i> Report Preview</h5>
        <div>
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <form method="get" action="<?= base_url('admin/reports/download/' . $format) ?>" style="display:inline;">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-download"></i> Download <?= strtoupper($format) ?>
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <h6 class="mb-3">Applied Filters:</h6>
        <div class="row mb-3">
            <?php foreach ($filters as $key => $value): ?>
                <?php if (!empty($value)): ?>
                    <div class="col-md-3">
                        <span class="badge bg-info">
                            <?= ucfirst(str_replace('_', ' ', $key)) ?> = <?= esc($value) ?>
                        </span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <h6 class="mb-3">Metrik yang Dipilih: <?= implode(', ', $metrics) ?></h6>

        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>User</th>
                        <?php if (in_array('application', $metrics)): ?><th>App</th><?php endif; ?>
                        <?php if (in_array('status', $metrics)): ?><th>Status</th><?php endif; ?>
                        <?php if (in_array('priority', $metrics)): ?><th>Priority</th><?php endif; ?>
                        <?php if (in_array('created', $metrics)): ?><th>Created</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($complaints)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No data matching the selected filters</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($complaints as $i => $c): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= $c->id ?></td>
                                <td><?= esc($c->title) ?></td>
                                <td><?= $c->user_id ?></td>
                                <?php if (in_array('application', $metrics)): ?><td><?= $c->application_id ?></td><?php endif; ?>
                                <?php if (in_array('status', $metrics)): ?>
                                    <td><span class="badge bg-<?= $c->status == 'pending' ? 'warning' : ($c->status == 'resolved' ? 'success' : 'secondary') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $c->status)) ?>
                                        </span></td>
                                <?php endif; ?>
                                <?php if (in_array('priority', $metrics)): ?>
                                    <td><?= ucfirst($c->priority) ?></td>
                                <?php endif; ?>
                                <?php if (in_array('created', $metrics)): ?>
                                    <td><?= date('Y-m-d', strtotime($c->created_at)) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> Total records: <strong><?= count($complaints) ?></strong>
        </div>
    </div>
</div>
<?= $this->endSection() ?>