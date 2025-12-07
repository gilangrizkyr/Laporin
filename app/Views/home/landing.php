<?= $this->extend('layout/guest') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>Laporkan Kendala Aplikasi dengan Mudah</h1>
                <p>Sistem pengaduan terintegrasi untuk melaporkan error, bug, dan kendala pada aplikasi internal DPMPTSP Tanah Bumbu.</p>
                <div class="d-flex gap-3">
                    <?php if (session()->has('user_id')): ?>
                        <a href="<?= base_url(session()->get('role') . '/dashboard') ?>" class="btn btn-light btn-custom">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('auth/register') ?>" class="btn btn-light btn-custom">
                            <i class="fas fa-user-plus"></i> Mulai Sekarang
                        </a>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-outline-light btn-custom">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-clipboard-check" style="font-size: 15rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="container mb-5">
    <div class="text-center mb-5">
        <h2>Statistik Pengaduan</h2>
        <p class="text-muted">Data real-time sistem pengaduan</p>
    </div>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="stats-card">
                <div class="number"><?= number_format($stats['total']) ?></div>
                <div class="label">Total Laporan</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stats-card">
                <div class="number text-info"><?= number_format($stats['in_progress']) ?></div>
                <div class="label">Sedang Diproses</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stats-card">
                <div class="number text-success"><?= number_format($stats['resolved']) ?></div>
                <div class="label">Selesai</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="container mb-5">
    <div class="text-center mb-5">
        <h2>Keunggulan Sistem</h2>
        <p class="text-muted">Mengapa menggunakan sistem pengaduan kami?</p>
    </div>
    <div class="row">
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h4>Cepat & Mudah</h4>
            <p class="text-muted">Laporkan kendala hanya dalam beberapa klik. Interface yang user-friendly dan intuitif.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h4>Chat Internal</h4>
            <p class="text-muted">Komunikasi langsung dengan admin melalui chat internal tanpa perlu email.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4>Tracking Real-time</h4>
            <p class="text-muted">Pantau status pengaduan Anda secara real-time dengan notifikasi otomatis.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h4>Aman & Privat</h4>
            <p class="text-muted">Data pengaduan Anda aman dan hanya dapat dilihat oleh Anda dan admin.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h4>Upload Bukti</h4>
            <p class="text-muted">Upload screenshot, video, atau dokumen sebagai bukti pendukung laporan.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon">
                <i class="fas fa-book"></i>
            </div>
            <h4>Knowledge Base</h4>
            <p class="text-muted">Cari solusi masalah umum di knowledge base sebelum membuat laporan.</p>
        </div>
    </div>
</section>

<!-- Recent Complaints -->
<section class="container mb-5">
    <div class="text-center mb-5">
        <h2>Laporan Terbaru</h2>
        <p class="text-muted">Transparansi pengaduan yang sedang ditangani</p>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($recentComplaints)): ?>
                <?php foreach ($recentComplaints as $complaint): ?>
                    <div class="card complaint-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-2">
                                        <span class="badge bg-secondary me-2">#<?= $complaint->id ?></span>
                                        <?= esc($complaint->title) ?>
                                    </h5>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-desktop"></i> <?= esc($complaint->application_name) ?>
                                        <span class="ms-3"><i class="fas fa-clock"></i> <?= $complaint->getCreatedDiff() ?></span>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <?= $complaint->getPriorityBadge() ?>
                                    <?= $complaint->getStatusBadge() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada laporan terbaru.
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Popular KB Articles -->
<?php if (!empty($popularArticles)): ?>
<section class="container mb-5">
    <div class="text-center mb-5">
        <h2>Artikel Populer</h2>
        <p class="text-muted">Solusi masalah yang sering terjadi</p>
    </div>
    <div class="row">
        <?php foreach ($popularArticles as $article): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($article->title) ?></h5>
                        <p class="card-text text-muted"><?= esc($article->getExcerpt(100)) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> <?= number_format($article->view_count) ?> views
                            </small>
                            <a href="<?= base_url('knowledge-base/' . $article->id) ?>" class="btn btn-sm btn-outline-success">
                                Baca <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="<?= base_url('knowledge-base') ?>" class="btn btn-success">
            Lihat Semua Artikel <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- How to Use -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Cara Menggunakan</h2>
            <p class="text-muted">Mudah dan cepat dalam 4 langkah</p>
        </div>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-success mb-3">1</div>
                <h5>Register / Login</h5>
                <p class="text-muted">Daftar atau login ke akun Anda</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-success mb-3">2</div>
                <h5>Buat Laporan</h5>
                <p class="text-muted">Isi form laporan dengan detail lengkap</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-success mb-3">3</div>
                <h5>Tunggu Respon</h5>
                <p class="text-muted">Admin akan segera menangani laporan Anda</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-success mb-3">4</div>
                <h5>Selesai</h5>
                <p class="text-muted">Beri feedback setelah masalah selesai</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="container my-5 py-5 text-center">
    <h2 class="mb-4">Siap Melaporkan Kendala?</h2>
    <p class="lead mb-4">Bergabunglah dengan sistem pengaduan modern kami</p>
    <?php if (!session()->has('user_id')): ?>
        <a href="<?= base_url('auth/register') ?>" class="btn btn-success btn-lg btn-custom me-3">
            <i class="fas fa-user-plus"></i> Daftar Sekarang
        </a>
        <a href="<?= base_url('knowledge-base') ?>" class="btn btn-outline-success btn-lg btn-custom">
            <i class="fas fa-book"></i> Lihat Knowledge Base
        </a>
    <?php else: ?>
        <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success btn-lg btn-custom">
            <i class="fas fa-plus-circle"></i> Buat Laporan Baru
        </a>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>