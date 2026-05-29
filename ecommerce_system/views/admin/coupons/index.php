<?php $pageTitle = 'Coupons'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-ticket me-2"></i>Coupons</h4>
    <a href="<?= url('admin/coupons/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Coupon</a>
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
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Min Order</th>
                        <th>Uses</th>
                        <th>Max Uses</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($coupons)): ?>
                    <?php foreach ($coupons as $coupon): ?>
                    <?php
                    $isExpired = $coupon['expires_at'] && strtotime($coupon['expires_at']) < time();
                    if ($isExpired):
                        $statusClass = 'secondary';
                        $statusLabel = 'Expired';
                    elseif ($coupon['is_active']):
                        $statusClass = 'success';
                        $statusLabel = 'Active';
                    else:
                        $statusClass = 'danger';
                        $statusLabel = 'Inactive';
                    endif;
                    ?>
                    <tr>
                        <td><span class="fw-medium"><?= htmlspecialchars($coupon['code']) ?></span></td>
                        <td><span class="fw-semibold"><?= $coupon['type'] === 'percentage' ? $coupon['value'] . '%' : formatPrice($coupon['value']) ?></span></td>
                        <td><small><?= $coupon['min_order_amount'] > 0 ? formatPrice($coupon['min_order_amount']) : '-' ?></small></td>
                        <td><small><?= (int)($coupon['uses_count'] ?? 0) ?></small></td>
                        <td><small><?= $coupon['max_uses'] ?? '∞' ?></small></td>
                        <td><small class="text-muted"><?= $coupon['expires_at'] ? date('M j, Y', strtotime($coupon['expires_at'])) : '-' ?></small></td>
                        <td><span class="badge bg-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                        <td>
                            <a href="<?= url('admin/coupons/edit/' . $coupon['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= url('admin/coupons/delete/' . $coupon['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></a>
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
