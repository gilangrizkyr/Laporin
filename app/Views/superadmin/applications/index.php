<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h3>Applications</h3>
    <a href="<?= base_url('superadmin/applications/create') ?>" class="btn btn-primary btn-sm">Create Application</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Critical</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($apps as $a): ?>
                    <tr>
                        <td><?= $no++ ?></td> <!-- nomor urut -->
                        <td><?= esc($a->name) ?></td>
                        <td><?= $a->is_critical ? 'Yes' : 'No' ?></td>
                        <td><?= $a->is_active ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="<?= base_url('superadmin/applications/' . $a->id . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?= route_to('superadmin.applications.delete', $a->id) ?>" method="post" style="display:inline-block" onsubmit="return confirm('Delete application?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
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