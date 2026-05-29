<?php $pageTitle = 'Edit Profile'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-pencil me-2"></i>Edit Profile</h3>
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="list-group border-0 shadow-sm">
                <a href="<?= url('profile') ?>" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="<?= url('orders') ?>" class="list-group-item list-group-item-action"><i class="bi bi-box me-2"></i>My Orders</a>
                <a href="<?= url('wishlist') ?>" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i>Wishlist</a>
                <a href="<?= url('profile/edit') ?>" class="list-group-item list-group-item-action active"><i class="bi bi-pencil me-2"></i>Edit Profile</a>
                <a href="<?= url('profile/password') ?>" class="list-group-item list-group-item-action"><i class="bi bi-lock me-2"></i>Change Password</a>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <?php if ($msg = flash('success')): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('profile/edit') ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" name="address_line1" class="form-control" value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" name="address_line2" class="form-control" value="<?= htmlspecialchars($user['address_line2'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">County</label>
                                <input type="text" name="county" class="form-control" value="<?= htmlspecialchars($user['county'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="zip_code" class="form-control" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
