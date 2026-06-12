<?php
/** @var array $order */
/** @var array $items */
$pageTitle = 'Order #' . $order['order_number']; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-cart me-2"></i>Order #<?= htmlspecialchars($order['order_number']) ?></h4>
    <div>
        <a href="<?= url('admin/orders/invoice/' . $order['id']) ?>" class="btn btn-outline-primary no-print me-2" target="_blank"><i class="bi bi-receipt me-1"></i>Invoice</a>
        <button class="btn btn-outline-secondary no-print me-2" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>
<?php if ($msg = flash('success')): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Order Items</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?= productImg($item['product_image'] ?? null, $item['product_name'], 50, 50) ?>" 
                                         alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= formatPrice($item['price']) ?></td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td class="text-end"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-medium">Subtotal</td>
                                <td class="text-end"><?= formatPrice($order['subtotal']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-medium">Shipping</td>
                                <td class="text-end"><?= formatPrice($order['shipping_amount']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-medium">Tax</td>
                                <td class="text-end"><?= formatPrice($order['tax_amount']) ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total</td>
                                <td class="text-end"><?= formatPrice((float)($order['total_amount'] ?? $order['total'] ?? 0)) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-person me-1"></i>Customer Information</h6>
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?? '-') ?></p>
                <p class="mb-0"><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'] ?? '-')) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1"></i>Order Info</h6>
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Order #</td>
                        <td class="text-end"><?= htmlspecialchars($order['order_number']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date</td>
                        <td class="text-end"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment Method</td>
                        <td class="text-end"><?= htmlspecialchars($order['payment_method'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td class="text-end"><?= getStatusBadge($order['status']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment</td>
                        <td class="text-end">
                            <?php
                            $paymentClasses = ['completed' => 'success', 'pending' => 'warning', 'failed' => 'danger', 'refunded' => 'info'];
                            $pClass = $paymentClasses[$order['payment_status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $pClass ?>"><?= htmlspecialchars($order['payment_status']) ?></span>
                        </td>
                    </tr>
                    <?php if ($order['tracking_number']): ?>
                    <tr>
                        <td class="text-muted">Tracking</td>
                        <td class="text-end"><?= htmlspecialchars($order['tracking_number']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-1"></i>Update Order</h6>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Order Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $order['payment_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Tracking Number</label>
                        <input type="text" name="tracking_number" class="form-control" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>" placeholder="Optional">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
