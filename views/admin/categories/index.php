<?php
/** @var array $categories */
$pageTitle = 'Categories'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-tags me-2"></i>Categories</h4>
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Category</a>
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
                        <th>Slug</th>
                        <th>Parent</th>
                        <th>Product Count</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><small><?= $cat['id'] ?></small></td>
                        <td><span class="fw-medium"><?= htmlspecialchars($cat['name']) ?></span></td>
                        <td><small class="text-muted"><?= htmlspecialchars($cat['slug']) ?></small></td>
                        <td><small><?= htmlspecialchars($cat['parent_name'] ?? '—') ?></small></td>
                        <td><span class="badge bg-info"><?= (int)($cat['product_count'] ?? 0) ?></span></td>
                        <td><?= getStatusBadge($cat['status']) ?></td>
                        <td>
                            <a href="<?= url('admin/categories/edit/' . $cat['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= url('admin/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
