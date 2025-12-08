<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs"></i> Custom Report Builder</h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= base_url('admin/reports/generate') ?>">
                    <?= csrf_field() ?>

                    <!-- Metrics Selection -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Select Metrics to Include</strong></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]" value="application" checked>
                                    <label class="form-check-label">Application</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]" value="category" checked>
                                    <label class="form-check-label">Category</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]" value="status" checked>
                                    <label class="form-check-label">Status</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]" value="priority" checked>
                                    <label class="form-check-label">Priority</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]" value="created" checked>
                                    <label class="form-check-label">Created Date</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="metrics[]" value="resolved" checked>
                                    <label class="form-check-label">Resolved Date</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Application</label>
                            <select name="app_id" class="form-select">
                                <option value="">All Applications</option>
                                <?php foreach ($applications as $app): ?>
                                    <?php $appId = is_array($app) ? ($app['id'] ?? '') : ($app->id ?? ''); ?>
                                    <?php $appName = is_array($app) ? ($app['name'] ?? '') : ($app->name ?? ''); ?>
                                    <option value="<?= esc($appId) ?>"><?= esc($appName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <?php $catId = is_array($cat) ? ($cat['id'] ?? '') : ($cat->id ?? ''); ?>
                                    <?php $catName = is_array($cat) ? ($cat['name'] ?? '') : ($cat->name ?? ''); ?>
                                    <option value="<?= esc($catId) ?>"><?= esc($catName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">All</option>
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">All</option>
                                <?php foreach ($admins as $admin): ?>
                                    <?php $adminId = is_array($admin) ? ($admin['id'] ?? '') : ($admin->id ?? ''); ?>
                                    <?php $adminName = is_array($admin) ? ($admin['full_name'] ?? '') : ($admin->full_name ?? ''); ?>
                                    <option value="<?= esc($adminId) ?>"><?= esc($adminName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- Format Selection -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Export Format</strong></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="pdf" id="formatPdf" checked>
                            <label class="form-check-label" for="formatPdf">
                                <i class="fas fa-file-pdf" style="color: #f44336;"></i> PDF
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="excel" id="formatExcel">
                            <label class="form-check-label" for="formatExcel">
                                <i class="fas fa-file-excel" style="color: #4CAF50;"></i> Excel
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-magic"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="fas fa-info-circle"></i> Tips</h6>
                <ul class="list-unstyled" style="font-size: 12px;">
                    <li><strong>✓</strong> Pilih metrik yang ingin Anda sertakan dalam laporan</li>
                    <li><strong>✓</strong> Terapkan filter untuk mempersempit data</li>
                    <li><strong>✓</strong> Choose between PDF or Excel format</li>
                    <li><strong>✓</strong> Review the preview before downloading</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Export History</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">No exports yet</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>