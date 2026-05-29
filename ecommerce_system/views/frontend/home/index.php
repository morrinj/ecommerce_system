<?php $pageTitle = 'Home'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<section class="hero-section">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide d-flex align-items-center text-white" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 520px;">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-lg-7">
                                <span class="badge bg-warning text-dark mb-3 px-3 py-2">New Season Collection</span>
                                <h1 class="display-3 fw-bold mb-3">Elevate Your Style</h1>
                                <p class="lead mb-4 fs-5 opacity-90">Discover the latest fashion trends. From casual chic to formal elegance — find your perfect look today.</p>
                                <a href="<?= url('products') ?>" class="btn btn-light btn-lg px-5 fw-semibold rounded-pill">Shop Now <i class="bi bi-arrow-right ms-2"></i></a>
                                <a href="<?= url('products?on_sale=1') ?>" class="btn btn-outline-light btn-lg px-4 fw-semibold rounded-pill ms-2">Sale <i class="bi bi-tags ms-1"></i></a>
                            </div>
                            <div class="col-lg-5 text-center d-none d-lg-block">
                                <i class="bi bi-handbag-fill" style="font-size: 14rem; opacity: 0.1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide d-flex align-items-center text-white" style="background: linear-gradient(135deg, #2d1b69 0%, #6c3483 50%, #a569bd 100%); min-height: 520px;">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-lg-7">
                                <span class="badge bg-light text-dark mb-3 px-3 py-2">Free Shipping</span>
                                <h1 class="display-3 fw-bold mb-3">Free Delivery Over <?= formatPriceNoDecimal(SHIPPING_THRESHOLD) ?></h1>
                                <p class="lead mb-4 fs-5 opacity-90">Shop your favorite fashion items and enjoy free delivery on all orders above <?= formatPriceNoDecimal(SHIPPING_THRESHOLD) ?>. Limited time offer!</p>
                                <a href="<?= url('products') ?>" class="btn btn-light btn-lg px-5 fw-semibold rounded-pill">Start Shopping <i class="bi bi-arrow-right ms-2"></i></a>
                            </div>
                            <div class="col-lg-5 text-center d-none d-lg-block">
                                <i class="bi bi-truck" style="font-size: 14rem; opacity: 0.1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide d-flex align-items-center text-white" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 50%, #40916c 100%); min-height: 520px;">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-lg-7">
                                <span class="badge bg-success mb-3 px-3 py-2">Premium Quality</span>
                                <h1 class="display-3 fw-bold mb-3">Curated Fashion</h1>
                                <p class="lead mb-4 fs-5 opacity-90">Hand-picked styles from top brands. Quality fabrics, modern designs, and timeless classics all in one place.</p>
                                <a href="<?= url('products') ?>" class="btn btn-light btn-lg px-5 fw-semibold rounded-pill">Browse Collection <i class="bi bi-arrow-right ms-2"></i></a>
                            </div>
                            <div class="col-lg-5 text-center d-none d-lg-block">
                                <i class="bi bi-gem" style="font-size: 14rem; opacity: 0.1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Featured Fashion</h2>
                <p class="text-muted">Curated picks just for you</p>
            </div>
            <a href="<?= url('products?is_featured=1') ?>" class="btn btn-outline-dark rounded-pill">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php if (empty($featuredProducts)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-box-seam display-1 text-muted"></i>
                <p class="mt-3 text-muted">No featured products yet.</p>
            </div>
            <?php else: ?>
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
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
                                <i class="bi bi-star<?= $i <= round($product['average_rating']) ? '-fill text-warning' : ($i - 0.5 <= $product['average_rating'] ? '-half text-warning' : '-fill text-muted') ?>" style="font-size: 0.75rem;"></i>
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
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-4 col-lg-<?= 12 / max(1, count($categories)) ?>">
                <a href="<?= url('products?category=' . $cat['slug']) ?>" class="text-decoration-none">
                    <div class="category-card card border-0 shadow-sm text-center p-4 h-100">
                        <div class="category-icon mb-3">
                            <i class="bi bi-<?= $cat['icon'] ?? 'grid' ?> display-5"></i>
                        </div>
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                        <small class="text-muted"><?= $cat['product_count'] ?? 0 ?> Products</small>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($newArrivals)): ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">New Arrivals</h2>
                <p class="text-muted">Fresh off the runway</p>
            </div>
            <a href="<?= url('products?is_new=1') ?>" class="btn btn-outline-dark rounded-pill">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($newArrivals as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">New</span>
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
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <i class="bi bi-robot display-3 mb-3 d-block opacity-50"></i>
                <h2 class="fw-bold mb-3">AI Style Assistant</h2>
                <p class="lead mb-4 opacity-75">Get personalized fashion recommendations based on your style preferences and browsing history.</p>
                <div id="ai-recommendations" class="row g-3 mt-3">
                    <?php if (isLoggedIn()): ?>
                    <p class="text-light opacity-75">Loading personalized recommendations...</p>
                    <?php else: ?>
                    <p class="text-light opacity-75"><a href="<?= url('login') ?>" class="text-white fw-bold">Log in</a> to see personalized fashion picks</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($onSaleProducts)): ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Hot Deals</h2>
                <p class="text-muted">Limited time fashion offers</p>
            </div>
            <a href="<?= url('products?on_sale=1') ?>" class="btn btn-outline-danger rounded-pill">All Deals <i class="bi bi-lightning ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($onSaleProducts as $product): ?>
            <div class="col-6 col-md-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                            -<?= round((1 - $product['price'] / $product['compare_price']) * 100) ?>%
                        </span>
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
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold fs-5 text-danger"><?= formatPrice($product['price']) ?></span>
                                <span class="text-decoration-line-through text-muted small"><?= formatPrice($product['compare_price']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-truck display-5 text-primary"></i>
                    <h6 class="fw-bold mt-3">Free Shipping</h6>
                    <small class="text-muted">On orders over <?= formatPriceNoDecimal(SHIPPING_THRESHOLD) ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-shield-check display-5 text-primary"></i>
                    <h6 class="fw-bold mt-3">Secure Payment</h6>
                    <small class="text-muted">100% secure checkout</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-arrow-return-left display-5 text-primary"></i>
                    <h6 class="fw-bold mt-3">Easy Returns</h6>
                    <small class="text-muted">30-day return policy</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-headset display-5 text-primary"></i>
                    <h6 class="fw-bold mt-3">24/7 Support</h6>
                    <small class="text-muted">AI-powered assistance</small>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
