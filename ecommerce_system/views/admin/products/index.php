<?php $pageTitle = 'Products'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-box me-2"></i>Products</h4>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Product</a>
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
                        <th>Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="<?= productImg($product['image_primary'] ?? null, $product['name'], 50, 50) ?>" 
                                 alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                        </td>
                        <td><span class="fw-medium"><?= htmlspecialchars(truncate($product['name'], 40)) ?></span></td>
                        <td><small><?= htmlspecialchars($product['sku'] ?? '-') ?></small></td>
                        <td><small><?= htmlspecialchars($product['category_name'] ?? '-') ?></small></td>
                        <td><span class="fw-semibold"><?= formatPrice($product['price']) ?></span></td>
                        <td><span class="badge bg-<?= $product['stock_quantity'] <= 5 ? 'danger' : ($product['stock_quantity'] <= 15 ? 'warning' : 'success') ?>"><?= $product['stock_quantity'] ?></span></td>
                        <td><?= getStatusBadge($product['status']) ?></td>
                        <td>
                            <a href="<?= url('admin/products/edit/' . $product['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= url('admin/products/delete/' . $product['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
