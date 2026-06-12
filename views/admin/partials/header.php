<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - <?= APP_NAME ?> Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    <script>
    (function() {
        var theme = localStorage.getItem('admin_theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <a href="<?= url('admin') ?>" class="text-decoration-none text-white">
                <i class="bi bi-shield-fill me-2"></i><?= APP_NAME ?>
            </a>
        </div>
        <?php
        $adminRoute = $path[1] ?? '';
        $isActive = fn($r) => $adminRoute === $r || ($adminRoute === '' && $r === 'dashboard');
        ?>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('dashboard') ? 'active' : '' ?>" href="<?= url('admin') ?>">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-section mt-3 mb-1 px-3"><small>STORE</small></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('products') ? 'active' : '' ?>" href="<?= url('admin/products') ?>">
                        <i class="bi bi-box me-2"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('categories') ? 'active' : '' ?>" href="<?= url('admin/categories') ?>">
                        <i class="bi bi-tags me-2"></i>Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('orders') ? 'active' : '' ?>" href="<?= url('admin/orders') ?>">
                        <i class="bi bi-cart me-2"></i>Orders
                    </a>
                </li>
                <?php if (isSuperAdmin()): ?>
                <li class="nav-section mt-3 mb-1 px-3"><small>PEOPLE</small></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('users') ? 'active' : '' ?>" href="<?= url('admin/users') ?>">
                        <i class="bi bi-people me-2"></i>Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('admins') ? 'active' : '' ?>" href="<?= url('admin/admins') ?>">
                        <i class="bi bi-shield me-2"></i>Admins
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-section mt-3 mb-1 px-3"><small>ENGAGE</small></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('reviews') ? 'active' : '' ?>" href="<?= url('admin/reviews') ?>">
                        <i class="bi bi-star me-2"></i>Reviews
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('coupons') ? 'active' : '' ?>" href="<?= url('admin/coupons') ?>">
                        <i class="bi bi-ticket me-2"></i>Coupons
                    </a>
                </li>
                <li class="nav-section mt-3 mb-1 px-3"><small>INSIGHTS</small></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('reports') ? 'active' : '' ?>" href="<?= url('admin/reports') ?>">
                        <i class="bi bi-graph-up me-2"></i>Reports
                    </a>
                </li>
                <?php if (isSuperAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('ai') ? 'active' : '' ?>" href="<?= url('admin/ai') ?>">
                        <i class="bi bi-robot me-2"></i>AI Features
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-section mt-3 mb-1 px-3"><small>SYSTEM</small></li>
                <?php if (isSuperAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('settings') ? 'active' : '' ?>" href="<?= url('admin/settings') ?>">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('profile') ? 'active' : '' ?>" href="<?= url('admin/profile/password') ?>">
                        <i class="bi bi-key me-2"></i>Change Password
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('permissions') ? 'active' : '' ?>" href="<?= url('admin/permissions') ?>">
                        <i class="bi bi-shield-check me-2"></i>My Permissions
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-danger" href="<?= url('admin/logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="document.querySelector('.admin-sidebar').classList.toggle('show')">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="fw-semibold"><?= $pageTitle ?? 'Dashboard' ?></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                        <i class="bi bi-moon-stars" id="themeIcon"></i>
                    </button>
                    <span class="text-muted small d-none d-md-inline"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                    <?php if (isset($_SESSION['admin_role'])): ?>
                    <span class="badge bg-<?= $_SESSION['admin_role'] === 'superadmin' ? 'danger' : ($_SESSION['admin_role'] === 'admin' ? 'primary' : 'secondary') ?> d-none d-md-inline"><?= ucfirst($_SESSION['admin_role']) ?></span>
                    <?php endif; ?>
                    <a href="<?= url() ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-shop me-1"></i>Store</a>
                </div>
            </div>
        </header>
        <div class="admin-content p-3 p-lg-4">
<script>
function toggleTheme() {
    var html = document.documentElement;
    var current = html.getAttribute('data-theme');
    var next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('admin_theme', next);
    var icon = document.getElementById('themeIcon');
    if (icon) icon.className = next === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
}
document.addEventListener('DOMContentLoaded', function() {
    var theme = document.documentElement.getAttribute('data-theme');
    var icon = document.getElementById('themeIcon');
    if (icon) icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
});
</script>
