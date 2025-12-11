<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h3>Manajemen Pengguna</h3>
    <a href="<?= base_url('superadmin/users/create') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($users as $u): ?>
                    <tr <?= !$u->is_active ? 'class="table-warning"' : '' ?>>
                        <td><?= $no++ ?></td>
                        <td><?= esc($u->full_name) ?></td>
                        <td><?= esc($u->username) ?></td>
                        <td><?= esc($u->email) ?></td>
                        <td><span class="badge bg-<?= $u->role === 'superadmin' ? 'danger' : ($u->role === 'admin' ? 'info' : 'secondary') ?>">
                                <?= ucfirst($u->role) ?>
                            </span></td>
                        <td>
                            <?php if ($u->is_active): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Belum Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('superadmin/users/' . $u->id . '/edit') ?>"
                                class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Tombol Aktifkan / Nonaktifkan -->
                            <form action="<?= base_url('superadmin/users/toggle/' . $u->id) ?>" method="post" style="display:inline-block;">
                                <?= csrf_field() ?>
                                <button type="submit"
                                    class="btn btn-sm <?= $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                    onclick="return confirm('<?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?> akun ini?')">
                                    <i class="fas fa-power-off"></i>
                                    <?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>

                            <!-- Hapus -->
                            <form action="<?= route_to('superadmin.users.delete', $u->id) ?>" method="post" style="display:inline-block"
                                onsubmit="return confirm('Yakin hapus user ini?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
