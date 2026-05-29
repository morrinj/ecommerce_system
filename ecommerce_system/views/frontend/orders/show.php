<?php $pageTitle = 'Order #' . $order['order_number']; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('orders') ?>" class="text-decoration-none">My Orders</a></li>
            <li class="breadcrumb-item active">#<?= htmlspecialchars($order['order_number']) ?></li>
        </ol>
    </nav>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Order #<?= htmlspecialchars($order['order_number']) ?></h4>
                    <p class="text-muted mb-0">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="text-end">
                    <div class="mb-1"><?= getStatusBadge($order['status']) ?></div>
                    <div><?= getStatusBadge($order['payment_status']) ?></div>
                </div>
            </div>

            <div class="timeline mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-center">
                        <div class="rounded-circle bg-<?= in_array($order['status'], ['pending','processing','shipped','delivered']) ? 'success' : 'secondary' ?> text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <small class="d-block mt-1">Pending</small>
                    </div>
                    <div class="flex-grow-1 border-top border-<?= in_array($order['status'], ['processing','shipped','delivered']) ? 'success' : 'secondary' ?>"></div>
                    <div class="text-center">
                        <div class="rounded-circle bg-<?= in_array($order['status'], ['processing','shipped','delivered']) ? 'success' : 'secondary' ?> text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-gear"></i>
                        </div>
                        <small class="d-block mt-1">Processing</small>
                    </div>
                    <div class="flex-grow-1 border-top border-<?= in_array($order['status'], ['shipped','delivered']) ? 'success' : 'secondary' ?>"></div>
                    <div class="text-center">
                        <div class="rounded-circle bg-<?= in_array($order['status'], ['shipped','delivered']) ? 'success' : 'secondary' ?> text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-truck"></i>
                        </div>
                        <small class="d-block mt-1">Shipped</small>
                    </div>
                    <div class="flex-grow-1 border-top border-<?= $order['status'] === 'delivered' ? 'success' : 'secondary' ?>"></div>
                    <div class="text-center">
                        <div class="rounded-circle bg-<?= $order['status'] === 'delivered' ? 'success' : 'secondary' ?> text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-house-check"></i>
                        </div>
                        <small class="d-block mt-1">Delivered</small>
                    </div>
                </div>
            </div>

            <?php if ($order['tracking_number']): ?>
            <div class="alert alert-info">
                <strong>Tracking Number:</strong> <?= htmlspecialchars($order['tracking_number']) ?>
            </div>
            <?php endif; ?>

            <h6 class="fw-bold mb-3">Order Items</h6>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= formatPrice($item['price']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= formatPrice($item['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end">Subtotal</td><td><?= formatPrice($order['subtotal']) ?></td></tr>
                        <?php if ($order['discount_amount'] > 0): ?>
                        <tr><td colspan="3" class="text-end">Discount</td><td class="text-success">-<?= formatPrice($order['discount_amount']) ?></td></tr>
                        <?php endif; ?>
                        <tr><td colspan="3" class="text-end">Shipping</td><td><?= formatPrice($order['shipping_amount']) ?></td></tr>
                        <tr><td colspan="3" class="text-end">Tax</td><td><?= formatPrice($order['tax_amount']) ?></td></tr>
                        <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td><?= formatPrice($order['total_amount']) ?></td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-2">Shipping Address</h6>
                    <p class="mb-1"><?= htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['shipping_address1']) ?></p>
                    <?php if ($order['shipping_address2']): ?><p class="mb-1"><?= htmlspecialchars($order['shipping_address2']) ?></p><?php endif; ?>
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
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
