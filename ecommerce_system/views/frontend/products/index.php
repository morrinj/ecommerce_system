<?php $pageTitle = 'Products'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <?php if ($currentCategory): ?>
            <li class="breadcrumb-item"><a href="<?= url('products') ?>" class="text-decoration-none">Products</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($currentCategory['name']) ?></li>
            <?php else: ?>
            <li class="breadcrumb-item active">All Products</li>
            <?php endif; ?>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h6>
                    <form method="GET" action="<?= url('products') ?>" id="filterForm">
                        <?php if ($categorySlug = $_GET['category'] ?? null): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['slug'] ?>" <?= ($currentCategory && $currentCategory['id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="on_sale" class="form-check-input" id="onSale" value="1" <?= isset($_GET['on_sale']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="onSale">On Sale</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="in_stock" class="form-check-input" id="inStock" value="1" <?= isset($_GET['in_stock']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="inStock">In Stock Only</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
                        <a href="<?= url('products') ?>" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="text-muted"><?= $total ?> product<?= $total !== 1 ? 's' : '' ?> found</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted">Sort:</label>
                    <select name="sort" form="filterForm" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="newest" <?= (($_GET['sort'] ?? 'newest') === 'newest') ? 'selected' : '' ?>>Newest First</option>
                        <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name: A-Z</option>
                        <option value="rating" <?= ($_GET['sort'] ?? '') === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                    </select>
                </div>
            </div>
            <div class="row g-3">
                <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h5 class="mt-3">No products found</h5>
                    <p class="text-muted">Try adjusting your filters or search terms</p>
                    <a href="<?= url('products') ?>" class="btn btn-primary">View All Products</a>
                </div>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-4">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="position-relative">
                            <?php if ($product['is_on_sale'] && $product['compare_price']): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                            <?php endif; ?>
                            <?php if ($product['is_new']): ?>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">New</span>
                            <?php endif; ?>
                            <div class="product-img-wrapper" style="background-image: url('<?= productImg($product['image_primary'] ?? null, $product['name'], 300, 400) ?>')">
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted text-uppercase tracking-wide mb-1"><?= htmlspecialchars($product['category_name'] ?? '') ?></small>
                            <h6 class="card-title fw-semibold mb-2 d-flex align-items-center gap-2">
                                <a href="<?= url('product/' . $product['slug']) ?>" class="text-decoration-none text-dark stretched-link flex-grow-1"><?= htmlspecialchars($product['name']) ?></a>
                                <i class="bi bi-heart add-to-wishlist flex-shrink-0" data-product-id="<?= $product['id'] ?>" title="Add to Wishlist" style="position:static;"></i>
                            </h6>
                            <div class="mt-auto">
                                <?php if ($product['average_rating'] > 0): ?>
                                <div class="mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($product['average_rating']) ? '-fill text-warning' : '-fill text-muted' ?>" style="font-size: 0.7rem;"></i>
                                    <?php endfor; ?>
                                    <small class="text-muted">(<?= $product['review_count'] ?>)</small>
                                </div>
                                <?php endif; ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-5"><?= formatPrice($product['price']) ?></span>
                                    <?php if ($product['compare_price']): ?>
                                    <span class="text-decoration-line-through text-muted small"><?= formatPrice($product['compare_price']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php
            $totalPages = ceil($total / ITEMS_PER_PAGE);
            $prevPage = max(1, $page - 1);
            $nextPage = min($totalPages, $page + 1);
            $from = ($page - 1) * ITEMS_PER_PAGE + 1;
            $to = min($page * ITEMS_PER_PAGE, $total);
            $qs = $categorySlug ? '&category=' . urlencode($categorySlug) : '';
            $qs .= isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
            ?>
            <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted">Showing <?= $from ?>–<?= $to ?> of <?= $total ?></small>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $prevPage . $qs ?>">&laquo; Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">Page <?= $page ?> of <?= $totalPages ?></span>
                        </li>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $nextPage . $qs ?>">Next &raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
