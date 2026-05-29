<?php
$pageTitle = isset($admin) ? 'Edit Admin' : 'New Admin';
require __DIR__ . '/../partials/header.php';
$isEdit = isset($admin);
?>

<div class="card">
    <div class="card-header"><?= $isEdit ? 'Edit Administrator' : 'Create New Administrator' ?></div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password <?= $isEdit ? '(leave blank to keep current)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin" <?= ($admin['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="superadmin" <?= ($admin['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($admin['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($admin['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?> Admin</button>
                <a href="<?= url('admin/admins') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
