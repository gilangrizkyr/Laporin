<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #bdc3c7; padding: 10px; text-align: left; }
        th { background-color: #3498db; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 11px; }
        .status-pending { background: #f39c12; }
        .status-resolved { background: #27ae60; }
        .status-closed { background: #95a5a6; }
        .priority-urgent { background: #e74c3c; }
        .priority-important { background: #e67e22; }
        .priority-normal { background: #3498db; }
        .text-center { text-align: center; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENGADUAN SISTEM</h1>
        <p>Dibuat pada: <?= date('d F Y H:i') ?></p>
        <p>Total Pengaduan: <strong><?= count($complaints) ?></strong></p>
    </div>

    <?php if (!empty($filters)): ?>
    <div class="mt-20">
        <strong>Filter yang digunakan:</strong><br>
        <?php foreach ($filters as $key => $value): ?>
            <?php if ($value): ?>
                <?php
                    $label = ucwords(str_replace(['_id', '_'], ' ', $key));
                    if ($key === 'application_id') {
                        $app = (new \App\Models\ApplicationModel())->find($value);
                        $value = $app ? $app->name : '-';
                    } elseif ($key === 'category_id') {
                        $cat = (new \App\Models\CategoryModel())->find($value);
                        $value = $cat ? $cat->name : '-';
                    } elseif ($key === 'assigned_to') {
                        $user = (new \App\Models\UserModel())->find($value);
                        $value = $user ? $user->full_name : '-';
                    }
                ?>
                <small>• <?= $label ?>: <?= $value ?></small><br>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Judul Pengaduan</th>
                <th>Pengadu</th>
                <?php if (in_array('application', $metrics)): ?><th>Aplikasi</th><?php endif; ?>
                <?php if (in_array('category', $metrics)): ?><th>Kategori</th><?php endif; ?>
                <?php if (in_array('status', $metrics)): ?><th>Status</th><?php endif; ?>
                <?php if (in_array('priority', $metrics)): ?><th>Prioritas</th><?php endif; ?>
                <?php if (in_array('created', $metrics)): ?><th>Dibuat</th><?php endif; ?>
                <?php if (in_array('resolved', $metrics)): ?><th>Selesai</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($complaints as $i => $c): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= esc($c->title) ?></td>
                <td><strong><?= esc($c->user_full_name ?? 'Unknown User') ?></strong></td>
                
                <?php if (in_array('application', $metrics)): ?>
                    <td><?= esc($c->application_name ?? '-') ?></td>
                <?php endif; ?>

                <?php if (in_array('category', $metrics)): ?>
                    <td><?= esc($c->category_name ?? '-') ?></td>
                <?php endif; ?>

                <?php if (in_array('status', $metrics)): ?>
                    <td>
                        <span class="badge status-<?= $c->status ?>">
                            <?= ucfirst(str_replace('_', ' ', $c->status)) ?>
                        </span>
                    </td>
                <?php endif; ?>

                <?php if (in_array('priority', $metrics)): ?>
                    <td>
                        <span class="badge priority-<?= $c->priority ?>">
                            <?= ucfirst($c->priority) ?>
                        </span>
                    </td>
                <?php endif; ?>

                <?php if (in_array('created', $metrics)): ?>
                    <td><?= date('d-m-Y', strtotime($c->created_at)) ?></td>
                <?php endif; ?>

                <?php if (in_array('resolved', $metrics)): ?>
                    <td><?= $c->resolved_at ? date('d-m-Y', strtotime($c->resolved_at)) : '-' ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-20 text-center">
        <small>© <?= date('Y') ?> Sistem Pengaduan Internal - Dicetak otomatis oleh sistem</small>
    </div>
</body>
</html>