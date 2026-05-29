<?php $pageTitle = 'Change Password'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-lock me-2"></i>Change Password</h3>
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="list-group border-0 shadow-sm">
                <a href="<?= url('profile') ?>" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="<?= url('orders') ?>" class="list-group-item list-group-item-action"><i class="bi bi-box me-2"></i>My Orders</a>
                <a href="<?= url('wishlist') ?>" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i>Wishlist</a>
                <a href="<?= url('profile/edit') ?>" class="list-group-item list-group-item-action"><i class="bi bi-pencil me-2"></i>Edit Profile</a>
                <a href="<?= url('profile/password') ?>" class="list-group-item list-group-item-action active"><i class="bi bi-lock me-2"></i>Change Password</a>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <?php if ($msg = flash('success')): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>
                    <?php if ($msg = flash('error')): ?>
                    <div class="alert alert-danger"><?= $msg ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('profile/password') ?>">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
