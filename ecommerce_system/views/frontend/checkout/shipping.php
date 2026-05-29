<?php $pageTitle = 'Shipping Details'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="d-flex justify-content-center">
                <div class="checkout-steps">
                    <div class="step active">
                        <div class="step-circle">1</div>
                        <span class="step-label">Cart</span>
                    </div>
                    <div class="step-connector active"></div>
                    <div class="step active">
                        <div class="step-circle">2</div>
                        <span class="step-label">Shipping</span>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step">
                        <div class="step-circle">3</div>
                        <span class="step-label">Payment</span>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step">
                        <div class="step-circle">4</div>
                        <span class="step-label">Confirm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($msg = flash('success')): ?>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="alert alert-success alert-dismissible fade show"><?= $msg ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($msg = flash('error')): ?>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="alert alert-danger alert-dismissible fade show"><?= $msg ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-fill me-2 text-primary"></i>Customer Information</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form method="POST" action="<?= url('checkout/shipping') ?>" id="shippingForm" novalidate>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control form-control-lg <?= hasError('full_name') ? 'is-invalid' : '' ?>" placeholder="John Doe" value="<?= htmlspecialchars(old('full_name', $savedShipping['full_name'] ?? $_SESSION['user_name'] ?? '')) ?>" required>
                                <?php if ($err = error('full_name')): ?><div class="invalid-feedback"><?= $err ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg <?= hasError('email') ? 'is-invalid' : '' ?>" placeholder="john@example.com" value="<?= htmlspecialchars(old('email', $savedShipping['email'] ?? $_SESSION['user_email'] ?? '')) ?>" required>
                                <?php if ($err = error('email')): ?><div class="invalid-feedback"><?= $err ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control form-control-lg <?= hasError('phone') ? 'is-invalid' : '' ?>" placeholder="+254 712 345 678" value="<?= htmlspecialchars(old('phone', $savedShipping['phone'] ?? '')) ?>" required>
                                <?php if ($err = error('phone')): ?><div class="invalid-feedback"><?= $err ?></div><?php endif; ?>
                            </div>
                        </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Delivery Information</h5>
                </div>
                <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">County <span class="text-danger">*</span></label>
                                <select name="county" class="form-select form-select-lg <?= hasError('county') ? 'is-invalid' : '' ?>" required>
                                    <option value="">Select your county</option>
                                    <?php foreach ($counties as $county): ?>
                                    <option value="<?= $county ?>" <?= (old('county', $savedShipping['county'] ?? '') === $county) ? 'selected' : '' ?>><?= $county ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($err = error('county')): ?><div class="invalid-feedback"><?= $err ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Town / City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control form-control-lg <?= hasError('city') ? 'is-invalid' : '' ?>" placeholder="Nairobi" value="<?= htmlspecialchars(old('city', $savedShipping['city'] ?? '')) ?>" required>
                                <?php if ($err = error('city')): ?><div class="invalid-feedback"><?= $err ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Street Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control form-control-lg <?= hasError('address') ? 'is-invalid' : '' ?>" placeholder="Kaunda Street, 4th Floor" value="<?= htmlspecialchars(old('address', $savedShipping['address'] ?? '')) ?>" required>
                                <?php if ($err = error('address')): ?><div class="invalid-feedback"><?= $err ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Apartment / Building <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="apartment" class="form-control form-control-lg" placeholder="Suite 201" value="<?= htmlspecialchars(old('apartment', $savedShipping['apartment'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Postal Code <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="postal_code" class="form-control form-control-lg" placeholder="00100" value="<?= htmlspecialchars(old('postal_code', $savedShipping['postal_code'] ?? '')) ?>">
                            </div>
                        </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-truck me-2 text-primary"></i>Delivery Options</h5>
                </div>
                <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <?php foreach ($deliveryOptions as $key => $option): ?>
                            <div class="col-md-6">
                                <div class="delivery-option-card <?= (old('delivery_option', $savedShipping['delivery_option'] ?? 'standard') === $key) ? 'selected' : '' ?>">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="delivery_option" value="<?= $key ?>" id="delivery_<?= $key ?>" <?= (old('delivery_option', $savedShipping['delivery_option'] ?? 'standard') === $key) ? 'checked' : '' ?> required>
                                        <label class="form-check-label stretched-link" for="delivery_<?= $key ?>">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi <?= $option['icon'] ?> fs-4 text-primary"></i>
                                                <div>
                                                    <strong class="d-block"><?= $option['label'] ?></strong>
                                                    <small class="text-muted"><?= $option['days'] ?></small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <?php if ($err = error('delivery_option')): ?><div class="invalid-feedback d-block"><?= $err ?></div><?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-square-text me-2 text-primary"></i>Order Notes</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <textarea name="order_notes" class="form-control" rows="3" placeholder="Special instructions for delivery, gate code, landmark, etc."><?= htmlspecialchars(old('order_notes', $savedShipping['order_notes'] ?? '')) ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <a href="<?= url('cart') ?>" class="btn btn-outline-secondary btn-lg px-4"><i class="bi bi-arrow-left me-1"></i> Back to Cart</a>
                <button type="submit" class="btn btn-primary btn-lg px-5 flex-fill flex-md-grow-0"><i class="bi bi-credit-card me-1"></i> Continue to Payment</button>
            </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Order Summary</h6>

                    <div class="list-group list-group-flush mb-3">
                        <?php foreach ($items as $item): ?>
                        <div class="list-group-item d-flex gap-3 px-0 border-0">
                            <img src="<?= productImg($item['image_primary'] ?? null, $item['name'], 64, 64) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded" width="56" height="56" style="object-fit: cover;">
                            <div class="flex-grow-1 min-w-0">
                                <small class="d-block text-truncate fw-medium"><?= htmlspecialchars($item['name']) ?></small>
                                <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                            </div>
                            <small class="fw-medium text-nowrap"><?= formatPrice($item['price'] * $item['quantity']) ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cart Subtotal</span>
                        <span class="fw-medium" id="cartSubtotal"><?= formatPrice($subtotal) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Shipping</span>
                        <span class="fw-medium <?= $shipping['is_free'] ? 'text-success' : '' ?>" id="shippingDisplay">
                            <?= $shipping['is_free'] ? 'FREE' : formatPrice($shipping['cost']) ?>
                        </span>
                    </div>

                    <?php if ($shipping['is_free']): ?>
                    <div class="shipping-notification free-shipping" id="shippingNotification">
                        <i class="bi bi-truck"></i>
                        <span>Congratulations! Your order qualifies for <strong>FREE SHIPPING</strong>.</span>
                    </div>
                    <?php else: ?>
                    <div class="shipping-notification no-free-shipping" id="shippingNotification">
                        <i class="bi bi-info-circle"></i>
                        <span>Add <strong class="text-primary"><?= formatPrice($shipping['remaining']) ?></strong> more to qualify for <strong>FREE SHIPPING</strong>.</span>
                    </div>
                    <?php endif; ?>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong class="fs-5">Total</strong>
                        <strong class="fs-5 text-primary" id="cartTotal"><?= formatPrice($subtotal + $shipping['cost']) ?></strong>
                    </div>

                    <div class="ai-delivery-estimate p-3 rounded-3 mb-3">
                        <small class="text-muted fw-medium"><i class="bi bi-robot me-1"></i>AI Delivery Estimate</small>
                        <div class="mt-1">
                            <small class="text-muted d-block"><i class="bi bi-calendar-check me-1"></i><?= $aiPlaceholders['estimated_delivery'] ?></small>
                            <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i><?= $aiPlaceholders['smart_address_suggestions'] ?></small>
                            <small class="text-muted d-block"><i class="bi bi-star me-1"></i><?= $aiPlaceholders['delivery_recommendation'] ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2"></i>Why Shop With Us</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0"></i><small>Free shipping on orders over <?= formatPrice(SHIPPING_THRESHOLD) ?></small></li>
                        <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0"></i><small>Secure payment with M-Pesa & major cards</small></li>
                        <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0"></i><small>14-day easy return policy</small></li>
                        <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0"></i><small>Dedicated customer support</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
