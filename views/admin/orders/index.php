<?php
/** @var array $orders */
$pageTitle = 'Orders'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-cart me-2"></i>Orders</h4>
</div>
<?php if ($msg = flash('success')): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead class="table-light">
                    <tr>
                        <th>Order#</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <?php
                    $paymentClasses = ['completed' => 'success', 'pending' => 'warning', 'failed' => 'danger', 'refunded' => 'info'];
                    $paymentClass = $paymentClasses[$order['payment_status']] ?? 'secondary';
                    ?>
                    <tr>
                        <td><span class="fw-medium">#<?= htmlspecialchars($order['order_number']) ?></span></td>
                        <td><small><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></small></td>
                        <td><small><?= (int)($order['item_count'] ?? 0) ?></small></td>
                        <td><span class="fw-semibold"><?= formatPrice($order['total_amount']) ?></span></td>
                        <td><?= getStatusBadge($order['status']) ?></td>
                        <td><span class="badge bg-<?= $paymentClass ?>"><?= htmlspecialchars($order['payment_status']) ?></span></td>
                        <td><small><?= date('M j, Y', strtotime($order['created_at'])) ?></small></td>
                        <td>
                            <a href="<?= url('admin/orders/view/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
