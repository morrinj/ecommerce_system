<?php $pageTitle = 'Change Password'; require __DIR__ . '/partials/header.php'; ?>

<div class="card">
    <div class="card-header">Change Your Password</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
                <div class="form-text">
                    Must contain uppercase, lowercase, number, and special character.
                    Minimum <?= $_SESSION['admin_role'] === 'superadmin' ? 12 : 10 ?> characters.
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="<?= url('admin') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
