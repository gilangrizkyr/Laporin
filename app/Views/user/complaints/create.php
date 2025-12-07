<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Buat Laporan Baru</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle"></i> Buat Laporan Baru
                </h5>
            </div>
            <div class="card-body">
                <!-- Validation Errors -->
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user/complaints/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Application -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-desktop"></i> Aplikasi yang Bermasalah <span class="text-danger">*</span>
                        </label>
                        <select name="application_id" class="form-select" required>
                            <option value="">-- Pilih Aplikasi --</option>
                            <?php foreach ($applications as $app): ?>
                                <option value="<?= $app->id ?>" <?= old('application_id') == $app->id ? 'selected' : '' ?>>
                                    <?= esc($app->name) ?>
                                    <?php if ($app->isCritical()): ?>
                                        <span class="badge bg-danger">Critical</span>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pilih aplikasi yang mengalami kendala</small>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tag"></i> Kategori (Opsional)
                        </label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category->id ?>" <?= old('category_id') == $category->id ? 'selected' : '' ?>>
                                    <?= esc($category->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-heading"></i> Judul Laporan <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" class="form-control" 
                               placeholder="Contoh: Error saat login ke aplikasi SIMPEG" 
                               value="<?= old('title') ?>" required>
                        <small class="text-muted">Buat judul yang jelas dan singkat (minimal 5 karakter)</small>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i> Deskripsi / Kronologi <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" class="form-control" rows="6" 
                                  placeholder="Jelaskan detail masalah yang terjadi, kapan terjadi, dan langkah-langkah yang sudah dicoba..." 
                                  required><?= old('description') ?></textarea>
                        <small class="text-muted">Jelaskan masalah dengan detail (minimal 10 karakter)</small>
                    </div>

                    <!-- Impact Type -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-exclamation-triangle"></i> Dampak / Kondisi <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="cannot_use" id="impact1" <?= old('impact_type') == 'cannot_use' ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="impact1">
                                        <strong class="text-danger">Tidak Bisa Digunakan</strong><br>
                                        <small class="text-muted">Aplikasi sama sekali tidak bisa diakses</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="specific_bug" id="impact2" <?= old('impact_type') == 'specific_bug' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="impact2">
                                        <strong class="text-warning">Bug Tertentu</strong><br>
                                        <small class="text-muted">Fitur tertentu bermasalah</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="slow_performance" id="impact3" <?= old('impact_type') == 'slow_performance' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="impact3">
                                        <strong class="text-info">Kinerja Lambat</strong><br>
                                        <small class="text-muted">Aplikasi lemot atau loading lama</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="other" id="impact4" <?= old('impact_type') == 'other' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="impact4">
                                        <strong>Lainnya</strong><br>
                                        <small class="text-muted">Masalah lain yang tidak termasuk di atas</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Prioritas akan ditentukan otomatis berdasarkan dampak dan kritikalitas aplikasi
                        </small>
                    </div>

                    <!-- File Attachments -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-paperclip"></i> Lampiran (Opsional)
                        </label>
                        <input type="file" name="attachments[]" class="form-control" multiple 
                               accept="image/*,video/*,.pdf,.doc,.docx">
                        <small class="text-muted">
                            Upload screenshot, video, atau dokumen pendukung. 
                            Max: 5MB (gambar), 50MB (video), 10MB (dokumen)
                        </small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane"></i> Kirim Laporan
                        </button>
                        <a href="<?= base_url('user/dashboard') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Tips -->
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="mb-3">
                    <i class="fas fa-lightbulb text-warning"></i> Tips Membuat Laporan
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check text-success"></i>
                        <strong>Judul Jelas</strong><br>
                        <small class="text-muted">Buat judul yang spesifik dan mudah dipahami</small>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check text-success"></i>
                        <strong>Detail Lengkap</strong><br>
                        <small class="text-muted">Jelaskan kronologi masalah dengan detail</small>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check text-success"></i>
                        <strong>Bukti Pendukung</strong><br>
                        <small class="text-muted">Upload screenshot atau video jika memungkinkan</small>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check text-success"></i>
                        <strong>Pilih Dampak yang Tepat</strong><br>
                        <small class="text-muted">Sistem akan menentukan prioritas secara otomatis</small>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Knowledge Base Link -->
        <div class="card border-primary mt-3">
            <div class="card-body">
                <h6 class="text-primary">
                    <i class="fas fa-book"></i> Sudah Cek Knowledge Base?
                </h6>
                <p class="text-muted mb-3">Mungkin solusi masalah Anda sudah ada di Knowledge Base</p>
                <a href="<?= base_url('knowledge-base') ?>" class="btn btn-outline-primary btn-sm w-100" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Buka Knowledge Base
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>