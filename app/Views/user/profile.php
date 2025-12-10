<?= $this->extend('layout/user') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> Profil Pengguna</h5>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="full_name" name="full_name"
                                    value="<?= esc($user['full_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= esc($user['email']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?= esc($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label">Departemen</label>
                                <input type="text" class="form-control" id="department" name="department"
                                    value="<?= esc($user['department'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= esc($user['address'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Foto Profil</h6>
            </div>
            <div class="card-body text-center">
                <div class="user-avatar mb-3" style="width: 100px; height: 100px; margin: 0 auto;">
                    <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                </div>
                <button class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-camera"></i> Ubah Foto
                </button>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Informasi Akun</h6>
            </div>
            <div class="card-body">
                <p><strong>Role:</strong> <?= ucfirst($user['role']) ?></p>
                <p><strong>Bergabung:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?></p>
                <p><strong>Terakhir Login:</strong> <?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Belum pernah' ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= base_url('user/profile/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', 'Terjadi kesalahan saat menyimpan data');
            });
    });

    function resetForm() {
        document.getElementById('profileForm').reset();
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;

        const container = document.querySelector('.content-area');
        container.insertAdjacentHTML('afterbegin', alertHtml);

        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
</script>
<?= $this->endSection() ?>