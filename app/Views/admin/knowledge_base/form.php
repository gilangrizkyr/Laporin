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
                        <!-- Ganti textarea dengan div Quill -->
                        <div id="editor" style="height: 400px;"><?= esc($article->content ?? old('content')) ?></div>
                        <input type="hidden" name="content" id="hidden-content">
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
                        <label class="form-check-label" for="is_published">Published</label>
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
<!-- Quill JS & CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>

<script>
    // Inisialisasi Quill
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['link', 'image', 'blockquote', 'code-block'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ header: [1, 2, 3, false] }],
                ['clean']
            ]
        }
    });

    // Submit form: isi hidden input dengan HTML dari Quill
    document.querySelector('form').onsubmit = function() {
        document.getElementById('hidden-content').value = quill.root.innerHTML;
    };
</script>
<?= $this->endSection() ?>
