<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h3>Categories</h3>
    <a href="<?= base_url('superadmin/categories/create') ?>" class="btn btn-primary btn-sm">Create Category</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($c->name) ?></td>
                        <td><?= $c->is_active ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="<?= base_url('superadmin/categories/' . $c->id . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?= base_url('superadmin/categories/' . $c->id . '/delete') ?>" method="post" style="display:inline-block" onsubmit="return confirm('Delete category?')">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<?= $this->endSection() ?>