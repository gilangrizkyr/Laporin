<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Analytics Report <?= esc($year) ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Sistem Pengaduan - Analytics</h2>
        <p>Tahun <?= esc($year) ?></p>
    </div>

    <h4>Complaints per Month</h4>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($months as $m => $count): ?>
                <tr>
                    <td><?= date('F', mktime(0, 0, 0, $m, 1, $year)) ?></td>
                    <td><?= $count ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Top Problematic Applications</h4>
    <table>
        <thead>
            <tr>
                <th>Application</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topApps as $app): ?>
                <tr>
                    <td><?= esc($app['name']) ?></td>
                    <td><?= $app['total_complaints'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Complaints by Priority</h4>
    <table>
        <thead>
            <tr>
                <th>Priority</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($priorities as $p): ?>
                <tr>
                    <td><?= esc($p['priority'] ?? $p['0'] ?? 'unknown') ?></td>
                    <td><?= esc($p['total'] ?? $p['1'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>