<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Pencarian</h5>
        <a href="<?= base_url('search') ?>" class="btn btn-sm btn-outline-secondary">Kembali ke Pencarian</a>
    </div>
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-md-4">
                <input type="text" name="q" value="<?= esc($q) ?>" class="form-control" placeholder="Cari query...">
            </div>
            <div class="col-md-3">
                <input type="date" name="from" value="<?= esc($from) ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" value="<?= esc($to) ?>" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Query</th>
                        <th>Results</th>
                        <th>IP</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada riwayat pencarian.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $r): ?>
                            <tr>
                                <td><?= ($i + 1) ?></td>
                                <td>
                                    <?php if (! empty($r['user_id']) && isset($users[$r['user_id']])): ?>
                                        <?= esc($users[$r['user_id']]) ?>
                                    <?php elseif (! empty($r['user_id'])): ?>
                                        <?= esc($r['user_id']) ?>
                                    <?php else: ?>
                                        <em>Guest</em>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($r['query']) ?></td>
                                <td><?= esc($r['results_count'] ?? 0) ?></td>
                                <td><?= esc($r['ip_address'] ?? '-') ?></td>
                                <td><?= esc($r['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager) && $pager): ?>
            <div class="mt-3">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>