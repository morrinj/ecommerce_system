<?php $pageTitle = 'Order Confirmation'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <i class="bi bi-check-circle-fill text-success display-1"></i>
        <h2 class="fw-bold mt-3">Order Placed Successfully!</h2>
        <p class="text-muted">Thank you for your purchase. Your order has been confirmed.</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="fw-bold mb-1">Order #<?= htmlspecialchars($order['order_number']) ?></h6>
                            <small class="text-muted">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></small>
                        </div>
                        <span class="badge bg-warning text-dark fs-6"><?= ucfirst($order['status']) ?></span>
                    </div>
                    <h6 class="fw-bold mb-3">Order Items</h6>
                    <div class="list-group list-group-flush mb-4">
                        <?php foreach ($items as $item): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="fw-medium"><?= htmlspecialchars($item['product_name']) ?></span>
                                <small class="d-block text-muted">Qty: <?= $item['quantity'] ?> × <?= formatPrice($item['price']) ?></small>
                            </div>
                            <span class="fw-semibold"><?= formatPrice($item['subtotal']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span><?= formatPrice($order['subtotal']) ?></span>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Discount</span>
                        <span class="text-success">-<?= formatPrice($order['discount_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Shipping</span>
                        <span><?= formatPrice($order['shipping_amount']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tax</span>
                        <span><?= formatPrice($order['tax_amount']) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total Charged</strong>
                        <strong class="fs-5"><?= formatPrice($order['total_amount']) ?></strong>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Shipping Address</h6>
                            <p class="mb-1"><?= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) ?></p>
                            <p class="mb-1"><?= htmlspecialchars($order['shipping_address1']) ?></p>
                            <?php if ($order['shipping_address2']): ?>
                            <p class="mb-1"><?= htmlspecialchars($order['shipping_address2']) ?></p>
                            <?php endif; ?>
                            <p><?= htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_county'] . ' ' . $order['shipping_zip']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Billing Address</h6>
                            <p class="mb-1"><?= htmlspecialchars($order['billing_first_name'] . ' ' . $order['billing_last_name']) ?></p>
                            <p class="mb-1"><?= htmlspecialchars($order['billing_address1']) ?></p>
                            <p><?= htmlspecialchars($order['billing_city'] . ', ' . $order['billing_county'] . ' ' . $order['billing_zip']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="<?= url('orders') ?>" class="btn btn-outline-primary me-2">View All Orders</a>
                <a href="<?= url() ?>" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
