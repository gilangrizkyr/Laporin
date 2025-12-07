<?= $this->extend('layout/guest') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-success text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-book"></i> Knowledge Base
                </h1>
                <p class="lead mb-0">Temukan solusi cepat untuk masalah yang sering terjadi</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= base_url('/') ?>" class="btn btn-light">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="<?= base_url('knowledge-base/search') ?>" method="get">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="text" name="q" class="form-control" placeholder="Cari artikel..." required>
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Articles List -->
<section class="container mb-5">
    <?php if (!empty($articles)): ?>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= base_url('knowledge-base/' . $article->id) ?>" class="text-decoration-none text-dark">
                                    <?= esc($article->title) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted">
                                <?= esc($article->getExcerpt(120)) ?>
                            </p>
                            
                            <!-- Tags -->
                            <?php if ($article->tags): ?>
                                <div class="mb-3">
                                    <?php foreach ($article->getTagsArray() as $tag): ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-tag"></i> <?= esc($tag) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye"></i> <?= number_format($article->view_count) ?> views
                                </small>
                                <a href="<?= base_url('knowledge-base/' . $article->id) ?>" class="btn btn-sm btn-outline-success">
                                    Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h5>Belum ada artikel</h5>
            <p class="mb-0">Artikel knowledge base akan segera ditambahkan.</p>
        </div>
    <?php endif; ?>
</section>

<!-- CTA -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h3 class="mb-3">Tidak Menemukan Solusi?</h3>
        <p class="text-muted mb-4">Buat laporan pengaduan dan tim kami akan membantu Anda</p>
        <?php if (session()->has('user_id')): ?>
            <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success btn-lg">
                <i class="fas fa-plus-circle"></i> Buat Laporan
            </a>
        <?php else: ?>
            <a href="<?= base_url('auth/login') ?>" class="btn btn-success btn-lg">
                <i class="fas fa-sign-in-alt"></i> Login untuk Melaporkan
            </a>
        <?php endif; ?>
    </div>
</section>

<style>
    .hover-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }
</style>

<?= $this->endSection() ?>
