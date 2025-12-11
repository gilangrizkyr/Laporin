<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            Preview Laporan
        </h4>
        <div class="btn-group" role="group">

            <!-- KEMBALI -->
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>

            <!-- PRINT (pakai PDF) -->
            <button onclick="printReport()" class="btn btn-warning btn-sm" title="Cetak laporan">
                <i class="fas fa-print me-1"></i> Print
            </button>

            <!-- DOWNLOAD PDF -->
            <a href="<?= base_url('admin/reports/download/pdf') ?>" 
               class="btn btn-danger btn-sm" 
               download 
               title="Download sebagai PDF">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>

            <!-- DOWNLOAD EXCEL -->
            <a href="<?= base_url('admin/reports/download/excel') ?>" 
               class="btn btn-success btn-sm" 
               title="Download sebagai Excel">
                <i class="fas fa-file-excel me-1"></i> Excel
            </a>

        </div>
    </div>

    <div class="card-body">

        <!-- Filter Aktif -->
        <?php if (!empty($filters)): ?>
        <div class="alert alert-info mb-4 d-flex align-items-center">
            <i class="fas fa-filter me-2"></i>
            <strong>Filter aktif:</strong>
            <?php foreach ($filters as $k => $v): if($v): ?>
                <span class="badge bg-primary ms-2">
                    <?= ucwords(str_replace(['_id','_'], ' ', $k)) ?>: 
                    <?= is_numeric($v) 
                        ? ($k=='application_id' 
                            ? ($this->applicationModel->find($v)->name ?? '-') 
                            : ($k=='assigned_to' 
                                ? ($this->userModel->find($v)->full_name ?? '-') 
                                : $v)) 
                        : $v 
                    ?>
                </span>
            <?php endif; endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Tabel Preview -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Judul Pengaduan</th>
                        <th>Pengadu</th>
                        <?php if(in_array('application',$metrics)): ?><th>Aplikasi</th><?php endif; ?>
                        <?php if(in_array('category',$metrics)): ?><th>Kategori</th><?php endif; ?>
                        <?php if(in_array('status',$metrics)): ?><th>Status</th><?php endif; ?>
                        <?php if(in_array('priority',$metrics)): ?><th>Prioritas</th><?php endif; ?>
                        <?php if(in_array('created',$metrics)): ?><th>Tanggal Dibuat</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($complaints)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Tidak ada data yang sesuai filter
                            </td>
                        </tr>
                    <?php else: foreach($complaints as $i => $c): ?>
                        <tr>
                            <td class="text-center fw-bold"><?= $i + 1 ?></td>
                            <td><?= esc($c->title) ?></td>
                            <td><strong><?= esc($c->user_full_name ?? 'Unknown') ?></strong></td>
                            <?php if(in_array('application',$metrics)): ?>
                                <td><?= esc($c->application_name ?? '-') ?></td>
                            <?php endif; ?>
                            <?php if(in_array('category',$metrics)): ?>
                                <td><?= esc($c->category_name ?? '-') ?></td>
                            <?php endif; ?>
                            <?php if(in_array('status',$metrics)): ?>
                                <td class="text-center">
                                    <span class="badge bg-<?= $c->status=='pending'?'warning':($c->status=='resolved'?'success':'secondary') ?>">
                                        <?= ucfirst(str_replace('_',' ',$c->status)) ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <?php if(in_array('priority',$metrics)): ?>
                                <td class="text-center">
                                    <span class="badge bg-<?= $c->priority=='urgent'?'danger':($c->priority=='important'?'warning':'info') ?>">
                                        <?= ucfirst($c->priority) ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <?php if(in_array('created',$metrics)): ?>
                                <td class="text-center"><?= date('d M Y', strtotime($c->created_at)) ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Total Data -->
        <div class="alert alert-success d-flex align-items-center">
            <i class="fas fa-database me-2"></i>
            <strong>Total: <?= count($complaints) ?> pengaduan</strong>
        </div>
    </div>
</div>

<!-- IFRAME untuk Print PDF -->
<iframe id="printFrame" style="height:0; width:0; border:0;"></iframe>

<script>
function printReport() {
    const iframe = document.getElementById('printFrame');
    const pdfUrl = '<?= base_url('admin/reports/download/pdf') ?>';

    iframe.src = pdfUrl + '?t=' + new Date().getTime();

    iframe.onload = function() {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (e) {
            alert('PDF berhasil dimuat. Silakan klik tombol Print di jendela PDF.');
        }
    };
}
</script>

<?= $this->endSection() ?>