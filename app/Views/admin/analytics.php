<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Analytics</h2>
        <p class="text-muted mb-0">Ringkasan performa sistem dan laporan</p>
    </div>
    <div class="btn-group">
        <a id="export-csv" href="<?= base_url('admin/analytics/export?year=' . $year) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
        <a id="export-pdf" href="<?= base_url('admin/analytics/export-pdf?year=' . $year) ?>" class="btn btn-outline-secondary btn-sm ms-2">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Total Laporan Per Bulan (<?= esc($year) ?>)</h5>
                <div>
                    <select id="select-year" class="form-select form-select-sm">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="chartMonths" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card mb-3">
            <div class="card-body text-center">
                <h6>Rata-rata Waktu Resolusi</h6>
                <h3 class="mb-0"><?= $avgResolution !== null ? esc($avgResolution) . ' jam' : 'N/A' ?></h3>
                <small class="text-muted">(resolusi untuk status 'resolved')</small>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6>Complaints per Priority</h6>
                <canvas id="chartPriority" height="160"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bug"></i> Top Problematic Applications</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($topApps)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Application</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($topApps as $app): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($app['name']) ?></td>
                                        <td class="text-end"><?= number_format($app['total_complaints']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data aplikasi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user-tie"></i> Admin Performance</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($adminPerformance)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Admin</th>
                                    <th class="text-end">Assigned</th>
                                    <th class="text-end">Resolved</th>
                                    <th class="text-end">Avg (hrs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($adminPerformance as $ap): ?>
                                    <tr>
                                        <td><?= esc($ap['name']) ?></td>
                                        <td class="text-end"><?= number_format($ap['total_assigned']) ?></td>
                                        <td class="text-end"><?= number_format($ap['resolved']) ?></td>
                                        <td class="text-end"><?= $ap['avg_resolution_hours'] !== null ? esc($ap['avg_resolution_hours']) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data admin aktif.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const ctx = document.getElementById('chartMonths').getContext('2d');
        const labels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];

        const chartMonths = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Laporan',
                    data: [],
                    borderColor: 'rgba(54,162,235,1)',
                    backgroundColor: 'rgba(54,162,235,0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true
            }
        });

        // Average resolution chart (per month)
        const actx = document.createElement('canvas');
        actx.id = 'chartAvgResolution';
        actx.height = 120;
        document.querySelector('.col-md-8 .card-body').appendChild(actx);
        const avgCtx = actx.getContext('2d');
        const chartAvg = new Chart(avgCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Avg Resolution (hrs)',
                    data: [],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            },
            options: {
                responsive: true
            }
        });

        // Priority chart
        const pctx = document.getElementById('chartPriority').getContext('2d');
        let priorities = <?= json_encode(array_values($priorities)) ?>;
        const pLabels = ['Normal', 'Important', 'Urgent'];
        const pColors = ['#6c757d', '#ffc107', '#dc3545'];

        new Chart(pctx, {
            type: 'doughnut',
            data: {
                labels: pLabels,
                datasets: [{
                    data: priorities,
                    backgroundColor: pColors
                }]
            },
            options: {
                responsive: true
            }
        });

        // Year selector behavior: fetch APIs for the selected year
        async function fetchAndRender(year) {
            try {
                // monthly totals
                const totalsResp = await fetch('<?= base_url('admin/analytics/api/monthly-totals') ?>?year=' + year);
                const totalsJson = await totalsResp.json();
                const totals = totalsJson.data || [];
                chartMonths.data.datasets[0].data = totals;
                chartMonths.update();

                // avg resolution
                const avgResp = await fetch('<?= base_url('admin/analytics/api/monthly-avg') ?>?year=' + year);
                const avgJson = await avgResp.json();
                const avgData = avgJson.data || [];
                chartAvg.data.datasets[0].data = avgData;
                chartAvg.update();

                // complaints by app
                const appResp = await fetch('<?= base_url('admin/analytics/api/by-app') ?>?year=' + year);
                const appJson = await appResp.json();
                renderAppChart(appJson.data || []);

                // priorities - we can refresh by fetching totals grouped by priority
                const priResp = await fetch('<?= base_url('admin/analytics') ?>?year=' + year);
                // server page still provides priorities inlined; but for a pure API, we keep current values
                // Optionally update priorities from an API in future
            } catch (err) {
                console.error(err);
            }
        }

        document.getElementById('select-year').addEventListener('change', function(e) {
            const y = e.target.value;
            fetchAndRender(y);
            // update export links
            document.querySelector('a[href*="analytics/export"]').href = '<?= base_url('admin/analytics/export') ?>?year=' + y;
            document.querySelector('a[href*="analytics/export-pdf"]').href = '<?= base_url('admin/analytics/export-pdf') ?>?year=' + y;
        });

        // initial fetch
        fetchAndRender(<?= (int)$year ?>);

        // render applications chart
        function renderAppChart(rows) {
            // create or reuse canvas
            let canvas = document.getElementById('chartApps');
            if (!canvas) {
                const container = document.querySelector('.col-md-6 .card-body');
                canvas = document.createElement('canvas');
                canvas.id = 'chartApps';
                canvas.height = 200;
                container.insertBefore(canvas, container.firstChild);
            }
            const appLabels = rows.map(r => r.name);
            const appData = rows.map(r => r.total);
            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: appLabels,
                    datasets: [{
                        label: 'Complaints',
                        data: appData,
                        backgroundColor: 'rgba(255,99,132,0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y'
                }
            });
        }
    })();
</script>
<?= $this->endSection() ?>