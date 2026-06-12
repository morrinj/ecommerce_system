<?php
/** @var array $items */
$pageTitle = 'My Wishlist'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-heart me-2"></i>My Wishlist</h3>
    <?php if (empty($items)): ?>
    <div class="text-center py-5">
        <i class="bi bi-heartbreak display-1 text-muted"></i>
        <h5 class="mt-3">Your wishlist is empty</h5>
        <p class="text-muted">Save items you love to your wishlist</p>
        <a href="<?= url('products') ?>" class="btn btn-primary">Browse Products</a>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $item): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 border-0 shadow-sm">
                <div class="position-relative">
                    <button class="add-to-wishlist position-absolute top-0 end-0 m-2" data-product-id="<?= $item['id'] ?>" title="Remove from Wishlist">
                        <i class="bi bi-heart-fill fs-5 text-danger"></i>
                    </button>
                    <div class="product-img-wrapper">
                        <img src="<?= productImg($item['image_primary'] ?? null, $item['name'], 300, 300) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="card-img-top product-img">
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-semibold">
                        <a href="<?= url('product/' . $item['slug']) ?>" class="text-decoration-none text-dark stretched-link"><?= htmlspecialchars($item['name']) ?></a>
                    </h6>
                    <div class="mt-auto">
                        <span class="fw-bold fs-5"><?= formatPrice($item['price']) ?></span>
                        <?php if ($item['compare_price']): ?>
                        <span class="text-decoration-line-through text-muted small ms-1"><?= formatPrice($item['compare_price']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
