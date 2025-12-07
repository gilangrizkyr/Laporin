<?= $this->extend('layout/guest') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-success text-white py-5">
    <div class="container">
        <h1 class="mb-3">
            <i class="fas fa-search"></i> Hasil Pencarian
        </h1>
        <p class="lead mb-0">
            <?php if ($keyword): ?>
                Menampilkan hasil untuk: <strong>"<?= esc($keyword) ?>"</strong>
            <?php else: ?>
                Masukkan kata kunci pencarian
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Search Section -->
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="<?= base_url('knowledge-base/search') ?>" method="get">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="text" name="q" class="form-control" placeholder="Cari artikel..." value="<?= esc($keyword) ?>" autofocus>
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="container mb-5">
    <?php if ($keyword && !empty($articles)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Ditemukan <strong><?= count($articles) ?></strong> artikel
        </div>
        
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
                                    Baca <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    <?php elseif ($keyword && empty($articles)): ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5>Tidak Ada Hasil</h5>
            <p class="mb-3">Tidak ditemukan artikel yang sesuai dengan kata kunci <strong>"<?= esc($keyword) ?>"</strong></p>
            <a href="<?= base_url('knowledge-base') ?>" class="btn btn-success">
                <i class="fas fa-list"></i> Lihat Semua Artikel
            </a>
        </div>
        
        <!-- Suggestion Box -->
        <div class="card shadow-sm mt-4">
            <div class="card-body text-center">
                <h5>Saran Pencarian:</h5>
                <ul class="list-unstyled mt-3">
                    <li class="mb-2">✓ Gunakan kata kunci yang lebih umum</li>
                    <li class="mb-2">✓ Periksa ejaan kata kunci</li>
                    <li class="mb-2">✓ Coba gunakan sinonim</li>
                </ul>
            </div>
        </div>
        
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h5>Mulai Pencarian</h5>
            <p class="mb-0">Masukkan kata kunci untuk mencari artikel knowledge base</p>
        </div>
    <?php endif; ?>
</section>

<!-- CTA -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h3 class="mb-3">Tidak Menemukan yang Anda Cari?</h3>
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

