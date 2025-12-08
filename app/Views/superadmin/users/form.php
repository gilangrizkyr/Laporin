<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">
        <h4><?= $page_title ?? 'User Form' ?></h4>
        <form method="post" action="<?= isset($user) ? base_url('superadmin/users/' . $user->id . '/update') : base_url('superadmin/users/store') ?>">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= esc($user->full_name ?? old('full_name')) ?>">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc($user->email ?? old('email')) ?>">
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select">
                    <option value="user" <?= (isset($user) && $user->role == 'user') ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= (isset($user) && $user->role == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="superadmin" <?= (isset($user) && $user->role == 'superadmin') ? 'selected' : '' ?>>Superadmin</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah password (untuk edit)</small>

            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" <?= (isset($user) && $user->is_active) ? 'checked' : '' ?> id="is_active">
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>