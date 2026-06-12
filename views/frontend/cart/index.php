<?php
/** @var array $items */
/** @var float $total */
$pageTitle = 'Shopping Cart'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>Shopping Cart</h3>
    <?php if (empty($items)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h5 class="mt-3">Your cart is empty</h5>
        <p class="text-muted">Looks like you haven't added anything yet</p>
        <a href="<?= url('products') ?>" class="btn btn-primary btn-lg">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <?php foreach ($items as $item): ?>
            <div class="card border-0 shadow-sm mb-3 cart-item">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?= productImg($item['image_primary'] ?? null, $item['name'], 100, 100) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded">
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-1">
                                <a href="<?= url('product/' . $item['slug']) ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($item['name']) ?></a>
                            </h6>
                            <small class="text-muted"><?= formatPrice($item['price']) ?> each</small>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="<?= url('cart/update') ?>" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <div class="input-group input-group-sm" style="width: 110px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.parentNode.querySelector('input').stepDown(); this.parentNode.querySelector('input').dispatchEvent(new Event('change'))">-</button>
                                    <input type="number" name="quantity" class="form-control text-center" value="<?= $item['quantity'] ?>" min="1" max="99" onchange="this.form.submit()">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.parentNode.querySelector('input').stepUp(); this.parentNode.querySelector('input').dispatchEvent(new Event('change'))">+</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="fw-bold fs-5"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                        </div>
                        <div class="col-md-1 text-end">
                            <form method="POST" action="<?= url('cart/remove') ?>" onsubmit="return confirm('Remove this item?')">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash fs-5"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Shipping</span>
                        <span><?= $total >= SHIPPING_THRESHOLD ? 'Free' : formatPrice(SHIPPING_FLAT_RATE) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tax</span>
                        <span><?= formatPrice(round($total * TAX_RATE, 2)) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong class="fs-5"><?= formatPrice($total + ($total >= SHIPPING_THRESHOLD ? 0 : SHIPPING_FLAT_RATE) + round($total * TAX_RATE, 2)) ?></strong>
                    </div>
                    <a href="<?= url('checkout/shipping') ?>" class="btn btn-primary w-100 py-2 fw-semibold">Proceed to Checkout</a>
                    <a href="<?= url('products') ?>" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
