<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Superadmin Dashboard</h2>
        <p class="text-muted mb-0">Overview sistem dan manajemen</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card p-3">
            <h6>Total Users</h6>
            <h3><?= number_format($totalUsers) ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h6>Total Complaints</h6>
            <h3><?= number_format($stats['total']) ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h6>Urgent Open</h6>
            <h3><?= number_format($stats['urgent'] ?? 0) ?></h3>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
