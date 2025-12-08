<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $page_title ?? 'Knowledge Base Form' ?></h5>
    </div>
    <div class="card-body">
        <form method="post" action="<?= isset($article) ? base_url('admin/knowledge-base/' . $article->id . '/update') : base_url('admin/knowledge-base/store') ?>">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= esc($article->title ?? old('title')) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="content-editor" class="form-control" rows="10" required><?= esc($article->content ?? old('content')) ?></textarea>
                        <small class="text-muted d-block mt-1">Rich text editor - HTML formatting supported</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" class="form-control" placeholder="e.g., complaint, troubleshoot, urgent" value="<?= esc($article->tags ?? old('tags')) ?>">
                        <small class="text-muted d-block mt-1">Comma-separated tags</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Application <span class="text-danger">*</span></label>
                        <select name="application_id" class="form-select" required>
                            <option value="">-- Select Application --</option>
                            <?php foreach ($apps as $app): ?>
                                <option value="<?= $app->id ?>" <?= (isset($article) && $article->application_id == $app->id) ? 'selected' : '' ?>>
                                    <?= esc($app->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= (isset($article) && $article->category_id == $cat->id) ? 'selected' : '' ?>>
                                    <?= esc($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_published" class="form-check-input" id="is_published" <?= (isset($article) && $article->is_published) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_published">
                            Published
                        </label>
                        <small class="text-muted d-block">Unchecked = Draft</small>
                    </div>

                    <?php if (isset($article)): ?>
                        <div class="alert alert-info">
                            <small>
                                <strong>Views:</strong> <?= (int)$article->view_count ?><br>
                                <strong>Created:</strong> <?= $article->created_at ?><br>
                                <strong>Updated:</strong> <?= $article->updated_at ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save Article</button>
                <a href="<?= base_url('admin/knowledge-base') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content-editor',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | link image media table | bullist numlist | blockquote codesample | emoticons wordcount',
        height: 400,
        menubar: 'file edit view insert format tools table help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
    });
</script>
<?= $this->endSection() ?>
