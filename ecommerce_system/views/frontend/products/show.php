<?php $pageTitle = $product['name']; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('products') ?>" class="text-decoration-none">Products</a></li>
            <?php if ($product['category_name']): ?>
            <li class="breadcrumb-item"><a href="<?= url('products?category=' . $product['category_slug']) ?>" class="text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="product-gallery card border-0 shadow-sm p-3">
                <div class="main-image mb-3">
                    <img src="<?= productImg($product['image_primary'] ?? null, $product['name'], 600, 600) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded" id="mainProductImage">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="product-details">
                <?php if ($product['category_name']): ?>
                <small class="text-muted text-uppercase tracking-wide fw-semibold"><?= htmlspecialchars($product['category_name']) ?></small>
                <?php endif; ?>
                <h1 class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if ($product['review_count'] > 0): ?>
                    <div>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= round($product['average_rating']) ? '-fill text-warning' : ($i - 0.5 <= $product['average_rating'] ? '-half text-warning' : '-fill text-muted') ?>"></i>
                        <?php endfor; ?>
                        <span class="text-muted ms-1">(<?= $product['review_count'] ?> reviews)</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="display-6 fw-bold text-primary"><?= formatPrice($product['price']) ?></span>
                    <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                    <span class="text-decoration-line-through text-muted fs-4"><?= formatPrice($product['compare_price']) ?></span>
                    <span class="badge bg-danger fs-6">-<?= round((1 - $product['price'] / $product['compare_price']) * 100) ?>% OFF</span>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($product['short_description'] ?? $product['description'])) ?></p>
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fw-semibold">Availability:</span>
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <span class="badge bg-success">In Stock (<?= $product['stock_quantity'] ?> units)</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['sku']): ?>
                    <small class="text-muted">SKU: <?= htmlspecialchars($product['sku']) ?></small>
                    <?php endif; ?>
                </div>
                <?php if ($product['stock_quantity'] > 0): ?>
                <form method="POST" action="<?= url('cart/add') ?>" class="mb-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 140px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="this.parentNode.querySelector('input').stepDown(); this.parentNode.querySelector('input').dispatchEvent(new Event('change'))">-</button>
                            <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                            <button type="button" class="btn btn-outline-secondary" onclick="this.parentNode.querySelector('input').stepUp(); this.parentNode.querySelector('input').dispatchEvent(new Event('change'))">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                        <button type="button" class="add-to-wishlist" data-product-id="<?= $product['id'] ?>" title="Add to Wishlist">
                            <i class="bi bi-heart<?= $inWishlist ? '-fill text-danger' : '' ?>"></i>
                        </button>
                    </div>
                </form>
                <?php endif; ?>
                <hr>
                <div class="d-flex gap-4 text-center">
                    <div>
                        <i class="bi bi-truck text-primary"></i>
                        <small class="d-block text-muted">Free shipping over <?= formatPrice(SHIPPING_THRESHOLD) ?></small>
                    </div>
                    <div>
                        <i class="bi bi-arrow-return-left text-primary"></i>
                        <small class="d-block text-muted">30-day returns</small>
                    </div>
                    <div>
                        <i class="bi bi-shield-check text-primary"></i>
                        <small class="d-block text-muted">Secure checkout</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews (<?= $product['review_count'] ?>)</button>
                </li>
            </ul>
            <div class="tab-content p-4 bg-white border border-top-0 rounded-bottom shadow-sm" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <p><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <?php if (empty($reviews)): ?>
                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($review['user_name'] ?? 'Anonymous') ?></strong>
                            <small class="text-muted"><?= date('M j, Y', strtotime($review['created_at'])) ?></small>
                        </div>
                        <div>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill text-warning' : '-fill text-muted' ?>" style="font-size: 0.8rem;"></i>
                            <?php endfor; ?>
                        </div>
                        <?php if ($review['title']): ?>
                        <strong class="d-block mt-1"><?= htmlspecialchars($review['title']) ?></strong>
                        <?php endif; ?>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (isLoggedIn()): ?>
                    <hr>
                    <h6 class="fw-bold">Write a Review</h6>
                    <form method="POST" action="<?= url('review') ?>">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="slug" value="<?= $product['slug'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>"><i class="bi bi-star-fill"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Review title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Share your thoughts..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($related)): ?>
    <section class="mt-5">
        <h3 class="fw-bold mb-4">Related Products</h3>
        <div class="row g-3">
            <?php foreach ($related as $rel): ?>
            <div class="col-6 col-md-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="product-img-wrapper">
                        <img src="<?= productImg($rel['image_primary'] ?? null, $rel['name'], 300, 300) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="card-img-top product-img">
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-semibold">
                            <a href="<?= url('product/' . $rel['slug']) ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($rel['name']) ?></a>
                        </h6>
                        <span class="fw-bold"><?= formatPrice($rel['price']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
