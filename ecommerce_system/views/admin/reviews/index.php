<?php $pageTitle = 'Reviews'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-star me-2"></i>Reviews</h4>
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
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pending)): ?>
                    <?php foreach ($pending as $review): ?>
                    <tr>
                        <td><span class="fw-medium"><?= htmlspecialchars($review['product_name']) ?></span></td>
                        <td><small><?= htmlspecialchars($review['user_name']) ?></small></td>
                        <td><span class="text-warning"><?= str_repeat('★', (int)$review['rating']) ?></span></td>
                        <td><small><?= htmlspecialchars(truncate($review['comment'] ?? '', 60)) ?></small></td>
                        <td><span class="badge bg-<?= $review['is_approved'] ? 'success' : 'warning' ?>"><?= $review['is_approved'] ? 'Approved' : 'Pending' ?></span></td>
                        <td><small><?= date('M j, Y', strtotime($review['created_at'])) ?></small></td>
                        <td>
                            <?php if (!$review['is_approved']): ?>
                            <a href="<?= url('admin/reviews/approve/' . $review['id']) ?>" class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></a>
                            <?php endif; ?>
                            <a href="<?= url('admin/reviews/delete/' . $review['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></a>
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
