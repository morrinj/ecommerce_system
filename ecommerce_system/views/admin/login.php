<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card border-0 shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock display-4 text-primary"></i>
                            <h4 class="fw-bold mt-2">Admin Login</h4>
                            <p class="text-muted">Sign in to your admin dashboard</p>
                        </div>
                        <?php if ($msg = flash('error')): ?>
                        <div class="alert alert-danger"><?= $msg ?></div>
                        <?php endif; ?>
                        <form method="POST" action="<?= url('admin/login') ?>">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" placeholder="admin@smartshop.com" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Sign In</button>
                        </form>
                        <hr class="my-4">
                        <a href="<?= url() ?>" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-left me-2"></i>Back to Store</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
