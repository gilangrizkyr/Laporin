<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints') ?>">Daftar Laporan</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/complaints/' . $complaint->id) ?>">Detail #<?= $complaint->id ?></a></li>
        <li class="breadcrumb-item active">Feedback</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-star"></i> Beri Feedback
                </h5>
            </div>
            <div class="card-body">
                <!-- Complaint Info -->
                <div class="alert alert-info">
                    <h6 class="mb-2">Laporan #<?= $complaint->id ?></h6>
                    <strong><?= esc($complaint->title) ?></strong><br>
                    <small class="text-muted">Aplikasi: <?= esc($complaint->application_name) ?></small>
                </div>

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

                <form action="<?= base_url('user/complaints/' . $complaint->id . '/feedback') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Rating -->
                    <div class="mb-4 text-center">
                        <label class="form-label d-block mb-3">
                            <h5>Bagaimana penilaian Anda terhadap penanganan laporan ini?</h5>
                        </label>
                        <div class="rating-stars mb-3" id="ratingStars">
                            <i class="far fa-star star" data-rating="1"></i>
                            <i class="far fa-star star" data-rating="2"></i>
                            <i class="far fa-star star" data-rating="3"></i>
                            <i class="far fa-star star" data-rating="4"></i>
                            <i class="far fa-star star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" required>
                        <div id="ratingLabel" class="text-muted"></div>
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-comment"></i> Komentar / Testimoni (Opsional)
                        </label>
                        <textarea name="comment" class="form-control" rows="5" 
                                  placeholder="Bagikan pengalaman Anda tentang penanganan laporan ini..."><?= old('comment') ?></textarea>
                        <small class="text-muted">Maksimal 500 karakter</small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane"></i> Kirim Feedback
                        </button>
                        <a href="<?= base_url('user/complaints/' . $complaint->id) ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="fas fa-info-circle text-info"></i> Informasi
                </h6>
                <ul class="mb-0 small">
                    <li class="mb-2">Feedback Anda sangat membantu kami untuk meningkatkan kualitas pelayanan</li>
                    <li class="mb-2">Setelah memberikan feedback, laporan akan otomatis ditutup</li>
                    <li class="mb-2">Anda tidak dapat mengubah feedback setelah dikirim</li>
                    <li>Feedback Anda akan membantu evaluasi kinerja admin</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .rating-stars {
        font-size: 3rem;
        cursor: pointer;
    }
    
    .rating-stars .star {
        color: #ddd;
        transition: all 0.2s;
        margin: 0 5px;
    }
    
    .rating-stars .star:hover,
    .rating-stars .star.active {
        color: #ffc107;
    }
    
    .rating-stars .star:hover {
        transform: scale(1.2);
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const stars = document.querySelectorAll('.star');
const ratingValue = document.getElementById('ratingValue');
const ratingLabel = document.getElementById('ratingLabel');
const submitBtn = document.getElementById('submitBtn');

const labels = {
    1: '⭐ Sangat Buruk',
    2: '⭐⭐ Buruk',
    3: '⭐⭐⭐ Cukup',
    4: '⭐⭐⭐⭐ Baik',
    5: '⭐⭐⭐⭐⭐ Sangat Baik'
};

let selectedRating = 0;

// Hover effect
stars.forEach(star => {
    star.addEventListener('mouseenter', function() {
        const rating = parseInt(this.getAttribute('data-rating'));
        highlightStars(rating);
    });
});

// Click to select
stars.forEach(star => {
    star.addEventListener('click', function() {
        selectedRating = parseInt(this.getAttribute('data-rating'));
        ratingValue.value = selectedRating;
        highlightStars(selectedRating);
        updateLabel(selectedRating);
        submitBtn.disabled = false;
        
        // Add active class
        stars.forEach(s => s.classList.remove('active'));
        for (let i = 0; i < selectedRating; i++) {
            stars[i].classList.add('active');
        }
    });
});

// Reset on mouse leave
document.getElementById('ratingStars').addEventListener('mouseleave', function() {
    if (selectedRating > 0) {
        highlightStars(selectedRating);
    } else {
        resetStars();
    }
});

function highlightStars(rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.style.color = '#ffc107';
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = '#ddd';
        }
    });
}

function resetStars() {
    stars.forEach(star => {
        if (!star.classList.contains('active')) {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = '#ddd';
        }
    });
}

function updateLabel(rating) {
    ratingLabel.textContent = labels[rating];
    ratingLabel.style.fontSize = '1.2rem';
    ratingLabel.style.fontWeight = 'bold';
    
    // Color based on rating
    if (rating <= 2) {
        ratingLabel.style.color = '#dc3545';
    } else if (rating === 3) {
        ratingLabel.style.color = '#ffc107';
    } else {
        ratingLabel.style.color = '#28a745';
    }
}
</script>
<?= $this->endSection() ?>
