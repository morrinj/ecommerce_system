<?php $pageTitle = 'Customers'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Customers</h4>
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
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><small><?= $user['id'] ?></small></td>
                        <td><span class="fw-medium"><?= htmlspecialchars($user['full_name']) ?></span></td>
                        <td><small><?= htmlspecialchars($user['email']) ?></small></td>
                        <td><small><?= (int)($user['order_count'] ?? 0) ?></small></td>
                        <td><small><?= formatPrice($user['total_spent'] ?? 0) ?></small></td>
                        <td><span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($user['status']) ?></span></td>
                        <td><small><?= date('M j, Y', strtotime($user['created_at'])) ?></small></td>
                        <td>
                            <a href="<?= url('admin/users/toggle/' . $user['id']) ?>" class="btn btn-sm btn-<?= $user['status'] === 'active' ? 'warning' : 'success' ?>" title="Toggle Status">
                                <i class="bi bi-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
