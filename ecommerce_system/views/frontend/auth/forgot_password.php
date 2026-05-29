<?php $pageTitle = 'Forgot Password'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-key display-4 text-primary"></i>
                        <h3 class="fw-bold mt-2">Forgot Password?</h3>
                        <p class="text-muted">Enter your email and we'll send you a reset link</p>
                    </div>
                    <?php if ($msg = flash('error')): ?>
                    <div class="alert alert-danger"><?= $msg ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('forgot-password') ?>">
                        <div class="mb-4">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="you@example.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Send Reset Link</button>
                    </form>
                    <p class="text-center mt-4 mb-0">
                        <a href="<?= url('login') ?>" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
