<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1976D2;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 9px;
        }

        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-left: 3px solid #1976D2;
        }

        .filters strong {
            display: block;
            margin-bottom: 5px;
        }

        .filter-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background: #1976D2;
            color: white;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #999;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>CUSTOM REPORT</h1>
        <p>Generated: <?= date('d/m/Y H:i') ?></p>
    </div>

    <div class="filters">
        <strong>Applied Filters:</strong>
        <?php if (!empty($filters['date_from'])): ?>
            <div class="filter-item">Date From: <?= $filters['date_from'] ?></div>
        <?php endif; ?>
        <?php if (!empty($filters['date_to'])): ?>
            <div class="filter-item">Date To: <?= $filters['date_to'] ?></div>
        <?php endif; ?>
        <?php if (!empty($filters['application_id'])): ?>
            <div class="filter-item">Application: <?= $filters['application_id'] ?></div>
        <?php endif; ?>
        <?php if (!empty($filters['status'])): ?>
            <div class="filter-item">Status: <?= ucfirst(str_replace('_', ' ', $filters['status'])) ?></div>
        <?php endif; ?>
        <?php if (!empty($filters['priority'])): ?>
            <div class="filter-item">Priority: <?= ucfirst($filters['priority']) ?></div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Title</th>
                <th>User</th>
                <?php if (in_array('application', $metrics)): ?><th>Application</th><?php endif; ?>
                <?php if (in_array('category', $metrics)): ?><th>Category</th><?php endif; ?>
                <?php if (in_array('status', $metrics)): ?><th>Status</th><?php endif; ?>
                <?php if (in_array('priority', $metrics)): ?><th>Priority</th><?php endif; ?>
                <?php if (in_array('created', $metrics)): ?><th>Created</th><?php endif; ?>
                <?php if (in_array('resolved', $metrics)): ?><th>Resolved</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($complaints)): ?>
                <tr>
                    <td colspan="10" style="text-align: center; color: #999;">No data</td>
                </tr>
            <?php else: ?>
                <?php foreach ($complaints as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $c->id ?></td>
                        <td><?= esc(substr($c->title, 0, 50)) ?></td>
                        <td><?= $c->user_id ?></td>
                        <?php if (in_array('application', $metrics)): ?><td><?= $c->application_id ?></td><?php endif; ?>
                        <?php if (in_array('category', $metrics)): ?><td><?= $c->category_id ?></td><?php endif; ?>
                        <?php if (in_array('status', $metrics)): ?><td><?= ucfirst(str_replace('_', ' ', $c->status)) ?></td><?php endif; ?>
                        <?php if (in_array('priority', $metrics)): ?><td><?= ucfirst($c->priority) ?></td><?php endif; ?>
                        <?php if (in_array('created', $metrics)): ?><td><?= date('Y-m-d', strtotime($c->created_at)) ?></td><?php endif; ?>
                        <?php if (in_array('resolved', $metrics)): ?><td><?= $c->resolved_at ? date('Y-m-d', strtotime($c->resolved_at)) : '-' ?></td><?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: <?= count($complaints) ?> | Report Type: Custom | System Pengaduan</p>
    </div>
</body>

</html>