<?php $pageTitle = isset($coupon) ? 'Edit Coupon' : 'Add Coupon'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-<?= isset($coupon) ? 'pencil' : 'plus-lg' ?> me-2"></i><?= $pageTitle ?></h4>
    <a href="<?= url('admin/coupons') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Coupon Code</label>
                        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($coupon['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Type</label>
                            <select name="type" class="form-select">
                                <option value="percentage" <?= (isset($coupon) && $coupon['type'] === 'percentage') ? 'selected' : '' ?>>Percentage</option>
                                <option value="fixed" <?= (isset($coupon) && $coupon['type'] === 'fixed') ? 'selected' : '' ?>>Fixed</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Value</label>
                            <input type="number" step="0.01" name="value" class="form-control" value="<?= $coupon['value'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Min Order Amount</label>
                            <input type="number" step="0.01" name="min_order_amount" class="form-control" value="<?= $coupon['min_order_amount'] ?? 0 ?>" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Max Uses (global)</label>
                            <input type="number" name="max_uses" class="form-control" value="<?= $coupon['max_uses'] ?? '' ?>" min="0" placeholder="Leave empty for unlimited">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Max Uses Per User</label>
                            <input type="number" name="max_uses_per_user" class="form-control" value="<?= $coupon['max_uses_per_user'] ?? 1 ?>" min="1">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Starts At</label>
                        <input type="datetime-local" name="starts_at" class="form-control" value="<?= isset($coupon) && $coupon['starts_at'] ? date('Y-m-d\TH:i', strtotime($coupon['starts_at'])) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Expires At</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="<?= isset($coupon) && $coupon['expires_at'] ? date('Y-m-d\TH:i', strtotime($coupon['expires_at'])) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" <?= (!isset($coupon) || $coupon['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary px-4"><?= isset($coupon) ? 'Update Coupon' : 'Create Coupon' ?></button>
            <a href="<?= url('admin/coupons') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
