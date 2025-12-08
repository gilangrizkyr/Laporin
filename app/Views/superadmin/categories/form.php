<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">
        <h4><?= $page_title ?? 'Category Form' ?></h4>
        <form method="post" action="<?= isset($category) ? base_url('superadmin/categories/' . $category->id . '/update') : base_url('superadmin/categories/store') ?>">
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
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?= esc($category->name ?? old('name')) ?>">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"><?= esc($category->description ?? old('description')) ?></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" <?= (isset($category) && $category->is_active) ? 'checked' : '' ?> id="is_active">
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>