<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            /* border: 2px solid #000000ff; */
            padding: 15px;
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }

        .header-info {
            text-align: center;
            flex: 1;
        }

        .header-info h1 {
            font-size: 16px;
            color: #1976D2;
            margin-bottom: 5px;
        }

        .header-info p {
            font-size: 10px;
            color: #555;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #1976D2;
            color: white;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-box {
            flex: 1;
            background: #f5f5f5;
            padding: 10px;
            margin: 0 4px;
            border-left: 4px solid #1976D2;
            text-align: center;
        }

        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #1976D2;
        }

        .stat-label {
            font-size: 10px;
            color: #555;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table th {
            background: #e8e8e8;
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 10px;
            text-align: left;
        }

        table td {
            padding: 5px;
            border: 1px solid #eee;
            font-size: 10px;
        }

        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .signature-block p {
            margin: 0;
            font-size: 10px;
        }

        .signature-block .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            color: #999;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER DENGAN LOGO -->
    <div class="header">
        <!-- <img src="<?= base_url('img/logo-dpmptsp.png') ?>" class="logo" alt=""> -->
        <div class="header-info">
            <h1>LAPORAN SISTEM APLIKASI LAPORIN</h1>
            <p>Pd. Butun, Kec. Batulicin, Kabupaten Tanah Bumbu, Kalimantan Selatan 72273</p>
            <p>Telp: (0518) 70664</p>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="section">
        <div class="section-title">Statistik Ringkas</div>
        <div class="stat-row">
            <div class="stat-box">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Laporan</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['pending'] ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['in_progress'] ?></div>
                <div class="stat-label">Dalam Proses</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['resolved'] ?></div>
                <div class="stat-label">Terselesaikan</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['closed'] ?></div>
                <div class="stat-label">Ditutup</div>
            </div>
        </div>
    </div>

    <!-- Laporan per Bulan -->
    <div class="section">
        <div class="section-title">Laporan per Bulan</div>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th style="text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <tr>
                        <td><?= date('F', mktime(0, 0, 0, $m, 1, $year)) ?></td>
                        <td style="text-align: center;"><?= $months[$m] ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <!-- Distribusi Prioritas -->
    <div class="section">
        <div class="section-title">Distribusi Prioritas</div>
        <table>
            <thead>
                <tr>
                    <th>Prioritas</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: center;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = array_sum($priorities); ?>
                <?php foreach ($priorities as $priority => $count): ?>
                    <tr>
                        <td><?= ucfirst($priority) ?></td>
                        <td style="text-align: center;"><?= $count ?></td>
                        <td style="text-align: center;"><?= $total > 0 ? round(($count / $total) * 100, 1) : 0 ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Top Aplikasi Bermasalah -->
    <div class="section">
        <div class="section-title">10 Aplikasi Teratas dengan Laporan Terbanyak</div>
        <table>
            <thead>
                <tr>
                    <th>Aplikasi</th>
                    <th style="text-align: center;">Total Laporan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($topApps)): ?>
                    <?php foreach ($topApps as $app): ?>
                        <tr>
                            <td><?= esc($app['name']) ?></td>
                            <td style="text-align: center;"><?= $app['total_complaints'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">Tidak ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TANDA TANGAN -->
    <div class="signature">
        <div class="signature-block">
            <p>Mengetahui,</p>
            <p>Kepala Bidang</p>
            <p class="name">..............................................</p>
            <p>NIP. </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Pengaduan</p>
        <p><?= date('d/m/Y H:i', time()) ?></p>
    </div>
</body>

</html>
