<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h3>Users</h3>
    <a href="<?= base_url('superadmin/users/create') ?>" class="btn btn-primary btn-sm">Create User</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($u->full_name) ?></td>
                        <td><?= esc($u->email) ?></td>
                        <td><?= esc($u->role) ?></td>
                        <td><?= $u->is_active ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="<?= base_url('superadmin/users/' . $u->id . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?= base_url('superadmin/users/' . $u->id . '/delete') ?>" method="post" style="display:inline-block" onsubmit="return confirm('Delete user?')">
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