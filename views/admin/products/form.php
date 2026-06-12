<?php
/** @var array|null $product */
/** @var array $categories */
$pageTitle = isset($product) ? 'Edit Product' : 'Add Product'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-<?= isset($product) ? 'pencil' : 'plus-lg' ?> me-2"></i><?= $pageTitle ?></h4>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Compare Price ($)</label>
                            <input type="number" step="0.01" name="compare_price" class="form-control" value="<?= $product['compare_price'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2"><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Description</label>
                        <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Stock Quantity</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?? 0 ?>" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Primary Image</label>
                        <input type="file" name="image_primary" class="form-control" accept="image/*">
                        <?php if (isset($product) && $product['image_primary']): ?>
                        <div class="mt-2">
                            <img src="<?= productImg($product['image_primary'] ?? null, $product['name'], 150, 150) ?>" alt="" class="rounded" style="max-width: 150px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= (isset($product) && $product['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (isset($product) && $product['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            <option value="draft" <?= (isset($product) && $product['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" <?= (isset($product) && $product['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isFeatured">Featured Product</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_new" class="form-check-input" id="isNew" value="1" <?= (isset($product) && $product['is_new']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isNew">New Arrival</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_on_sale" class="form-check-input" id="isOnSale" value="1" <?= (isset($product) && $product['is_on_sale']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isOnSale">On Sale</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_track_stock" class="form-check-input" id="isTrackStock" value="1" <?= (isset($product) && $product['is_track_stock']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isTrackStock">Track Stock</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary px-4"><?= isset($product) ? 'Update Product' : 'Create Product' ?></button>
            <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
