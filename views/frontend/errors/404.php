<?php $pageTitle = '404 - Page Not Found'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5 text-center">
    <div class="py-5">
        <h1 class="display-1 fw-bold text-muted">404</h1>
        <h4 class="mb-3">Page Not Found</h4>
        <p class="text-muted mb-4">The page you are looking for does not exist or has been moved.</p>
        <a href="<?= url() ?>" class="btn btn-primary btn-lg">Go Home</a>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
