<?php $pageTitle = 'Admin Management'; require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Manage Administrators</h5>
    <a href="<?= url('admin/admins/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Admin</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table datatable mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($admins)): foreach ($admins as $a): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= htmlspecialchars($a['full_name']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td><span class="badge bg-<?= $a['role'] === 'superadmin' ? 'danger' : 'primary' ?>"><?= ucfirst($a['role']) ?></span></td>
                    <td><span class="badge bg-<?= $a['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($a['status']) ?></span></td>
                    <td><?= !empty($a['last_login']) ? date('M d, Y H:i', strtotime($a['last_login'])) : '-' ?></td>
                    <td><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                    <td class="no-print">
                        <a href="<?= url('admin/admins/edit/' . $a['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <?php if ($a['id'] !== ($_SESSION['admin_id'] ?? 0)): ?>
                        <a href="<?= url('admin/admins/delete/' . $a['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
