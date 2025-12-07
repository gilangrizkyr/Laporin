<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints') ?>">Daftar Laporan</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints/' . $complaint->id) ?>">Detail #<?= $complaint->id ?></a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit"></i> Edit Laporan #<?= $complaint->id ?>
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

                <form action="<?= base_url('user/complaints/' . $complaint->id . '/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Application -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-desktop"></i> Aplikasi yang Bermasalah <span class="text-danger">*</span>
                        </label>
                        <select name="application_id" class="form-select" required>
                            <option value="">-- Pilih Aplikasi --</option>
                            <?php foreach ($applications as $app): ?>
                                <option value="<?= $app->id ?>" <?= $complaint->application_id == $app->id ? 'selected' : '' ?>>
                                    <?= esc($app->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tag"></i> Kategori (Opsional)
                        </label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category->id ?>" <?= $complaint->category_id == $category->id ? 'selected' : '' ?>>
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
                               value="<?= esc($complaint->title) ?>" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i> Deskripsi / Kronologi <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" class="form-control" rows="6" required><?= esc($complaint->description) ?></textarea>
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
                                           value="cannot_use" id="impact1" <?= $complaint->impact_type == 'cannot_use' ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="impact1">
                                        <strong class="text-danger">Tidak Bisa Digunakan</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="specific_bug" id="impact2" <?= $complaint->impact_type == 'specific_bug' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="impact2">
                                        <strong class="text-warning">Bug Tertentu</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="slow_performance" id="impact3" <?= $complaint->impact_type == 'slow_performance' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="impact3">
                                        <strong class="text-info">Kinerja Lambat</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="impact_type" 
                                           value="other" id="impact4" <?= $complaint->impact_type == 'other' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="impact4">
                                        <strong>Lainnya</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Perhatian:</strong><br>
            Anda hanya dapat mengedit laporan yang masih berstatus <strong>Pending</strong>.
            Setelah admin mulai menangani, laporan tidak dapat diedit lagi.
        </div>
    </div>
</div>

<?= $this->endSection() ?>