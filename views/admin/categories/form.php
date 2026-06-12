<?php
/** @var array|null $category */
/** @var array $parentCategories */
$pageTitle = isset($category) ? 'Edit Category' : 'Add Category'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-<?= isset($category) ? 'pencil' : 'plus-lg' ?> me-2"></i><?= $pageTitle ?></h4>
    <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" placeholder="Leave empty to auto-generate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $category['sort_order'] ?? 0 ?>" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($parentCategories as $parent): ?>
                            <option value="<?= $parent['id'] ?>" <?= (isset($category) && $category['parent_id'] == $parent['id']) ? 'selected' : '' ?>><?= htmlspecialchars($parent['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if (isset($category) && $category['image']): ?>
                        <div class="mt-2">
                            <img src="<?= url($category['image']) ?>" alt="" class="rounded" style="max-width: 150px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Icon (CSS class)</label>
                        <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($category['icon'] ?? '') ?>" placeholder="e.g. bi bi-laptop">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= (isset($category) && $category['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (isset($category) && $category['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            <option value="draft" <?= (isset($category) && $category['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" <?= (isset($category) && $category['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isFeatured">Featured Category</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary px-4"><?= isset($category) ? 'Update Category' : 'Create Category' ?></button>
            <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
