<?php
/** @var array $items */
/** @var float $total */
/** @var float $discount */
/** @var float $shipping */
/** @var float $tax */
/** @var float $grandTotal */
$pageTitle = 'Checkout'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-credit-card me-2"></i>Checkout</h3>
    <?php if ($msg = flash('error')): ?>
    <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="<?= url('checkout') ?>" id="checkoutForm">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i>Shipping Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="shipping[first_name]" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="shipping[last_name]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="shipping[phone]" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" name="shipping[address1]" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address Line 2 (Optional)</label>
                                <input type="text" name="shipping[address2]" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="shipping[city]" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">County</label>
                                <input type="text" name="shipping[county]" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ZIP</label>
                                <input type="text" name="shipping[zip]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-card-text me-2"></i>Payment Method</h6>
                        <div class="mb-3">
                            <select name="payment_method" class="form-select" required>
                                <option value="">Select payment method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cod">Cash on Delivery</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-semibold">Place Order - <?= formatPrice($grandTotal) ?></button>
            </form>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Order Summary</h6>
                    <div class="mb-3">
                        <form method="POST" action="<?= url('checkout/coupon') ?>" class="input-group">
                            <input type="text" name="coupon_code" class="form-control" placeholder="Coupon code" value="<?= htmlspecialchars($_SESSION['coupon_code'] ?? '') ?>">
                            <button type="submit" class="btn btn-outline-primary">Apply</button>
                        </form>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($items as $item): ?>
                        <div class="list-group-item d-flex justify-content-between px-0 border-0">
                            <small><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></small>
                            <small><?= formatPrice($item['price'] * $item['quantity']) ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>
                    <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Discount</span>
                        <span class="text-success">-<?= formatPrice($discount) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Shipping</span>
                        <span><?= $shipping > 0 ? formatPrice($shipping) : '<span class="text-success">Free</span>' ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tax</span>
                        <span><?= formatPrice($tax) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong class="fs-5"><?= formatPrice($grandTotal) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBilling() {
    document.getElementById('billingSection').style.display = document.getElementById('sameAsBilling').checked ? 'none' : 'block';
}
</script>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
