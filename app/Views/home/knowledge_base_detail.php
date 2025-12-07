<?= $this->extend('layout/guest') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('knowledge-base') ?>">Knowledge Base</a></li>
                <li class="breadcrumb-item active"><?= esc($article->title) ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Article Content -->
<section class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <article class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Title -->
                    <h1 class="mb-4"><?= esc($article->title) ?></h1>
                    
                    <!-- Meta Info -->
                    <div class="d-flex gap-4 mb-4 text-muted">
                        <span>
                            <i class="fas fa-calendar"></i> 
                            <?= date('d M Y', strtotime($article->created_at)) ?>
                        </span>
                        <span>
                            <i class="fas fa-eye"></i> 
                            <?= number_format($article->view_count) ?> views
                        </span>
                    </div>
                    
                    <!-- Tags -->
                    <?php if ($article->tags): ?>
                        <div class="mb-4">
                            <?php foreach ($article->getTagsArray() as $tag): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-tag"></i> <?= esc($tag) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Content -->
                    <div class="article-content">
                        <?= $article->content ?>
                    </div>
                </div>
            </article>
            
            <!-- Share & Action Buttons -->
            <div class="d-flex gap-2 mb-4">
                <button class="btn btn-outline-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button class="btn btn-outline-success" onclick="copyLink()">
                    <i class="fas fa-share"></i> Share Link
                </button>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bookmark"></i> Artikel Terkait
                        </h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($relatedArticles as $related): ?>
                            <a href="<?= base_url('knowledge-base/' . $related->id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= esc($related->title) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> <?= number_format($related->view_count) ?>
                                        </small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Help Box -->
            <div class="card shadow-sm bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-question-circle fa-3x text-success mb-3"></i>
                    <h5>Butuh Bantuan?</h5>
                    <p class="text-muted">Artikel ini tidak membantu? Buat laporan pengaduan.</p>
                    <?php if (session()->has('user_id')): ?>
                        <a href="<?= base_url('user/complaints/create') ?>" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Buat Laporan
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-success">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
    }
    
    .article-content h3 {
        margin-top: 30px;
        margin-bottom: 15px;
        color: #4CAF50;
    }
    
    .article-content ul, .article-content ol {
        margin-left: 20px;
        margin-bottom: 20px;
    }
    
    .article-content p {
        margin-bottom: 15px;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }
</style>

<script>
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link berhasil disalin!');
    }).catch(err => {
        console.error('Gagal menyalin link:', err);
    });
}
</script>

<?= $this->endSection() ?>

