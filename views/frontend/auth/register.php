<?php $pageTitle = 'Register'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container-fluid min-vh-100 d-flex align-items-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25" style="background: radial-gradient(circle at 80% 50%, #667eea 0%, transparent 50%), radial-gradient(circle at 20% 80%, #764ba2 0%, transparent 50%), radial-gradient(circle at 60% 20%, #f093fb 0%, transparent 50%);"></div>
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 rounded-4" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-person-plus display-4" style="color: rgba(255,255,255,0.8);"></i>
                            <h3 class="fw-bold mt-2 text-white">Create Account</h3>
                            <p class="text-white-50">Join us and start shopping</p>
                        </div>
                        <?php if ($msg = flash('error')): ?>
                        <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0"><?= $msg ?></div>
                        <?php endif; ?>
                        <form method="POST" action="<?= url('register') ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium text-white-50">First Name</label>
                                    <input type="text" name="first_name" class="form-control bg-transparent" placeholder="John" required style="color: white; border-color: rgba(255,255,255,0.2);">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium text-white-50">Last Name</label>
                                    <input type="text" name="last_name" class="form-control bg-transparent" placeholder="Doe" required style="color: white; border-color: rgba(255,255,255,0.2);">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-white-50">Email Address</label>
                                <input type="email" name="email" class="form-control bg-transparent" placeholder="you@example.com" required style="color: white; border-color: rgba(255,255,255,0.2);">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-white-50">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent" style="color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.2);">+254</span>
                                    <input type="tel" name="phone" class="form-control bg-transparent" placeholder="712345678" style="color: white; border-color: rgba(255,255,255,0.2);">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium text-white-50">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control bg-transparent border-end-0" placeholder="Min. 6 characters" required minlength="6" id="regPassword" style="color: white; border-color: rgba(255,255,255,0.2);">
                                        <button class="btn btn-outline-light bg-transparent border-start-0" type="button" onclick="togglePassword('regPassword', this)" style="border-color: rgba(255,255,255,0.2);">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium text-white-50">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirm" class="form-control bg-transparent border-end-0" placeholder="Repeat password" required id="regConfirmPassword" style="color: white; border-color: rgba(255,255,255,0.2);">
                                        <button class="btn btn-outline-light bg-transparent border-start-0" type="button" onclick="togglePassword('regConfirmPassword', this)" style="border-color: rgba(255,255,255,0.2);">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 mt-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">Create Account</button>
                        </form>
                        <p class="text-center mt-4 mb-0 text-white-50">
                            Already have an account? <a href="<?= url('login') ?>" class="text-decoration-none fw-semibold text-white">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    var field = document.getElementById(fieldId);
    var icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
