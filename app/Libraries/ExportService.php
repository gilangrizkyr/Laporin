<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class ExportService
{
    /**
     * Export data to Excel (CSV format)
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $filename Filename without extension
     * @return void (triggers download)
     */
    public function exportToExcel(array $data, array $headers, string $filename = 'export'): void
    {
        $csvContent = $this->arrayToCsv($data, $headers);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo $csvContent;
        exit;
    }

    /**
     * Convert array to CSV
     */
    protected function arrayToCsv(array $data, array $headers): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Export to PDF using DomPDF
     * 
     * @param string $html HTML content
     * @param string $filename Filename without extension
     * @param string $orientation portrait or landscape
     * @return void (triggers download)
     */
    public function exportToPdf(string $html, string $filename = 'export', string $orientation = 'portrait'): void
    {
        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();
        
        $dompdf->stream($filename . '.pdf', ['Attachment' => true]);
    }

    /**
     * Generate HTML for complaint detail report
     */
    public function generateComplaintReportHtml($complaint, $user, $application, $history): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Pengaduan</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 5px 0; }
                .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .info-table td { padding: 8px; border: 1px solid #ddd; }
                .info-table td:first-child { font-weight: bold; width: 30%; background: #f5f5f5; }
                .history-table { width: 100%; border-collapse: collapse; }
                .history-table th, .history-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                .history-table th { background: #4CAF50; color: white; }
                .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
                .badge-urgent { background: #dc3545; color: white; }
                .badge-important { background: #ffc107; color: black; }
                .badge-normal { background: #6c757d; color: white; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>LAPORAN PENGADUAN</h2>
                <p>Sistem Pengaduan Aplikasi Internal</p>
            </div>
            
            <table class="info-table">
                <tr>
                    <td>ID Laporan</td>
                    <td>#' . $complaint->id . '</td>
                </tr>
                <tr>
                    <td>Judul</td>
                    <td>' . $complaint->title . '</td>
                </tr>
                <tr>
                    <td>Pelapor</td>
                    <td>' . $user->full_name . '</td>
                </tr>
                <tr>
                    <td>Aplikasi</td>
                    <td>' . $application->name . '</td>
                </tr>
                <tr>
                    <td>Deskripsi</td>
                    <td>' . nl2br($complaint->description) . '</td>
                </tr>
                <tr>
                    <td>Dampak</td>
                    <td>' . $complaint->getImpactLabel() . '</td>
                </tr>
                <tr>
                    <td>Prioritas</td>
                    <td>' . strtoupper($complaint->priority) . '</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>' . strtoupper($complaint->status) . '</td>
                </tr>
                <tr>
                    <td>Tanggal Dibuat</td>
                    <td>' . date('d/m/Y H:i', strtotime($complaint->created_at)) . '</td>
                </tr>
            </table>
            
            <h3>Riwayat Penanganan</h3>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($history as $h) {
            $html .= '
                    <tr>
                        <td>' . date('d/m/Y H:i', strtotime($h->created_at)) . '</td>
                        <td>' . $h->getActionLabel() . '</td>
                        <td>' . ($h->description ?? '-') . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <p style="margin-top: 30px; font-size: 10px; color: #666;">
                Dicetak pada: ' . date('d/m/Y H:i:s') . '
            </p>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Generate HTML for complaint list report
     */
    public function generateComplaintListHtml(array $complaints, array $filters = []): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Daftar Laporan Pengaduan</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 11px; }
                .header { text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 6px; border: 1px solid #ddd; text-align: left; }
                th { background: #4CAF50; color: white; }
                .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>DAFTAR LAPORAN PENGADUAN</h2>
                <p>Periode: ' . date('d/m/Y') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Pelapor</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>';
        
        $no = 1;
        foreach ($complaints as $complaint) {
            $html .= '
                    <tr>
                        <td>' . $no++ . '</td>
                        <td>#' . $complaint->id . '</td>
                        <td>' . $complaint->title . '</td>
                        <td>' . ($complaint->user_full_name ?? '-') . '</td>
                        <td>' . strtoupper($complaint->priority) . '</td>
                        <td>' . strtoupper($complaint->status) . '</td>
                        <td>' . date('d/m/Y', strtotime($complaint->created_at)) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <p style="margin-top: 20px; font-size: 10px; color: #666;">
                Total: ' . count($complaints) . ' laporan | Dicetak: ' . date('d/m/Y H:i:s') . '
            </p>
        </body>
        </html>';
        
        return $html;
    }
}
