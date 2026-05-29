<?php $pageTitle = 'Search Results'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-1">Search Results</h3>
    <p class="text-muted mb-4">Showing results for "<?= htmlspecialchars($_GET['q'] ?? '') ?>"</p>
    <div class="row g-3">
        <?php if (empty($products)): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-search display-1 text-muted"></i>
            <h5 class="mt-3">No products found</h5>
            <p class="text-muted">Try different keywords or browse our categories</p>
            <a href="<?= url('products') ?>" class="btn btn-primary">Browse All Products</a>
        </div>
        <?php else: ?>
        <?php foreach ($products as $product): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 border-0 shadow-sm">
                <div class="position-relative">
                    <?php if ($product['is_on_sale'] && $product['compare_price']): ?>
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                    <?php endif; ?>
                    <div class="product-img-wrapper">
                        <img src="<?= productImg($product['image_primary'] ?? null, $product['name'], 300, 300) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img-top product-img">
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-semibold">
                        <a href="<?= url('product/' . $product['slug']) ?>" class="text-decoration-none text-dark stretched-link"><?= htmlspecialchars($product['name']) ?></a>
                    </h6>
                    <div class="mt-auto">
                        <span class="fw-bold"><?= formatPrice($product['price']) ?></span>
                        <?php if ($product['compare_price']): ?>
                        <span class="text-decoration-line-through text-muted ms-1 small"><?= formatPrice($product['compare_price']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
