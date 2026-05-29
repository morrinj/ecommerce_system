<?php $pageTitle = 'My Orders'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-box me-2"></i>My Orders</h3>
    <?php if (empty($orders)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <h5 class="mt-3">No orders yet</h5>
        <p class="text-muted">Start shopping to see your orders here</p>
        <a href="<?= url('products') ?>" class="btn btn-primary">Browse Products</a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                    <td><?= $orderModel = new Order(); $items = $orderModel->getItems($order['id']); echo array_sum(array_column($items, 'quantity')); ?></td>
                    <td><?= formatPrice($order['total_amount']) ?></td>
                    <td><?= getStatusBadge($order['status']) ?></td>
                    <td><?= getStatusBadge($order['payment_status']) ?></td>
                    <td><a href="<?= url('order/' . $order['order_number']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
