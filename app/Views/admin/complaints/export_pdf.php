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
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1976D2;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #1976D2;
            margin-bottom: 10px;
        }

        .row {
            display: flex;
            margin-bottom: 8px;
        }

        .col {
            flex: 1;
        }

        .col-label {
            font-weight: bold;
            width: 150px;
        }

        .col-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background: #e8e8e8;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .status-pending {
            color: #ff9800;
        }

        .status-in_progress {
            color: #2196F3;
        }

        .status-resolved {
            color: #4CAF50;
        }

        .status-closed {
            color: #999;
        }

        .priority-normal {
            color: #666;
        }

        .priority-important {
            color: #ff9800;
        }

        .priority-urgent {
            color: #f44336;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-in_progress {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-resolved {
            background: #d4edda;
            color: #155724;
        }

        .badge-closed {
            background: #e2e3e5;
            color: #383d41;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN KELUHAN</h1>
        <p>Nomor: <?= str_pad($complaint->id, 6, '0', STR_PAD_LEFT) ?> | Tanggal: <?= date('d/m/Y H:i', strtotime($complaint->created_at)) ?></p>
    </div>

    <!-- Informasi Umum -->
    <div class="section">
        <div class="section-title">Informasi Umum</div>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-label">Pengguna:</div>
                    <div class="col-value"><?= esc($user ? $user->full_name : '-') ?></div>
                </div>
                <div class="row">
                    <div class="col-label">Email:</div>
                    <div class="col-value"><?= esc($user ? $user->email : '-') ?></div>
                </div>
                <div class="row">
                    <div class="col-label">Aplikasi:</div>
                    <div class="col-value"><?= esc($app ? $app->name : '-') ?></div>
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <div class="col-label">Kategori:</div>
                    <div class="col-value"><?= esc($category ? $category->name : '-') ?></div>
                </div>
                <div class="row">
                    <div class="col-label">Prioritas:</div>
                    <div class="col-value">
                        <span class="badge <?= 'badge-' . strtolower($complaint->priority) ?>">
                            <?= ucfirst($complaint->priority) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Keluhan -->
    <div class="section">
        <div class="section-title">Detail Keluhan</div>
        <div class="row">
            <div class="col-label">Status:</div>
            <div class="col-value">
                <span class="badge <?= 'badge-' . str_replace('_', '', $complaint->status) ?>">
                    <?= ucfirst(str_replace('_', ' ', $complaint->status)) ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-label">Tipe Dampak:</div>
            <div class="col-value"><?= ucfirst(str_replace('_', ' ', $complaint->impact_type)) ?></div>
        </div>
        <div class="row">
            <div class="col-label">Judul:</div>
            <div class="col-value"><strong><?= esc($complaint->title) ?></strong></div>
        </div>
        <div class="row">
            <div class="col-label">Deskripsi:</div>
            <div class="col-value"><?= nl2br(esc($complaint->description)) ?></div>
        </div>
    </div>

    <!-- Status Penanganan -->
    <div class="section">
        <div class="section-title">Status Penanganan</div>
        <div class="row">
            <div class="col-label">Ditugaskan ke:</div>
            <div class="col-value"><?= esc($assignedTo ? $assignedTo->full_name : 'Belum ditugaskan') ?></div>
        </div>
        <div class="row">
            <div class="col-label">Tanggal Dibuat:</div>
            <div class="col-value"><?= date('d/m/Y H:i', strtotime($complaint->created_at)) ?></div>
        </div>
        <div class="row">
            <div class="col-label">Tanggal Diselesaikan:</div>
            <div class="col-value"><?= $complaint->resolved_at ? date('d/m/Y H:i', strtotime($complaint->resolved_at)) : 'Belum diselesaikan' ?></div>
        </div>
        <div class="row">
            <div class="col-label">Tanggal Ditutup:</div>
            <div class="col-value"><?= $complaint->closed_at ? date('d/m/Y H:i', strtotime($complaint->closed_at)) : 'Belum ditutup' ?></div>
        </div>
    </div>

    <!-- History -->
    <?php if (!empty($history)): ?>
        <div class="section">
            <div class="section-title">Riwayat Perubahan</div>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tindakan</th>
                        <th>Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                            <td><?= esc($h['action']) ?></td>
                            <td><?= esc($h['user_name'] ?? '-') ?></td>
                            <td><?= esc($h['details'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Feedback -->
    <?php if ($feedback): ?>
        <div class="section">
            <div class="section-title">Umpan Balik Pengguna</div>
            <div class="row">
                <div class="col-label">Rating:</div>
                <div class="col-value">
                    <?php for ($i = 0; $i < (int)$feedback->rating; $i++): ?>
                        ★
                    <?php endfor; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-label">Komentar:</div>
                <div class="col-value"><?= nl2br(esc($feedback->comment)) ?></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Pengaduan</p>
        <p><?= date('d/m/Y H:i', time()) ?></p>
    </div>
</body>

</html>