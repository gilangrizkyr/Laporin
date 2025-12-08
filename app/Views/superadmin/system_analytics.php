<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>System Analytics</h2>
        <p class="text-muted mb-0">System-wide complaint analytics and metrics</p>
    </div>
    <div>
        <form action="<?= base_url('superadmin/analytics') ?>" method="get" class="d-inline">
            <select name="year" class="form-select d-inline-block" style="width: 150px;" onchange="this.form.submit()">
                <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
        <a href="<?= base_url('superadmin/analytics/export?year=' . $year) ?>" class="btn btn-outline-secondary btn-sm">Export CSV</a>
        <a href="<?= base_url('superadmin/analytics/export-excel?year=' . $year) ?>" class="btn btn-outline-success btn-sm">Export Excel</a>
    </div>
</div>

<!-- Key Metrics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Average Resolution Time</h5>
                <h3 class="text-primary">
                    <?= $avgResolution !== null ? round($avgResolution, 2) : '0' ?>
                    <small class="text-muted">hours</small>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Year <?= $year ?></h5>
                <h3 class="text-info">
                    <?php $total = 0; foreach ($byMonth as $m) { $total += (int)$m['total']; } echo $total; ?>
                    <small class="text-muted">total complaints</small>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Chart -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Monthly Complaints - <?= $year ?></h5>
    </div>
    <div class="card-body">
        <canvas id="monthlyChart" height="80"></canvas>
    </div>
</div>

<!-- Top Applications -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Top Applications by Complaint Count</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($topApps)): ?>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Application</th>
                        <th class="text-end">Complaints</th>
                        <th class="text-end">%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalComplaints = array_sum(array_column($topApps, 'total_complaints')); ?>
                    <?php foreach ($topApps as $app): ?>
                        <tr>
                            <td><?= esc($app['name']) ?></td>
                            <td class="text-end"><?= $app['total_complaints'] ?></td>
                            <td class="text-end"><?= $totalComplaints > 0 ? round(($app['total_complaints'] / $totalComplaints) * 100, 2) : 0 ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No data available</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = <?= json_encode(array_column($byMonth, 'total')) ?>;
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Complaints',
                data: monthlyData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
<?= $this->endSection() ?>
