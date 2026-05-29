<?php $pageTitle = 'Reset Password'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock display-4 text-primary"></i>
                        <h3 class="fw-bold mt-2">Reset Password</h3>
                        <p class="text-muted">Enter your new password</p>
                    </div>
                    <?php if ($msg = flash('error')): ?>
                    <div class="alert alert-danger"><?= $msg ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('reset-password') ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? ($_GET['token'] ?? '')) ?>">
                        <div class="mb-3">
                            <label class="form-label fw-medium">New Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Min. 6 characters" required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Confirm New Password</label>
                            <input type="password" name="password_confirm" class="form-control form-control-lg" placeholder="Repeat password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
