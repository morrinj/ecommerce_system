<?php
$pageTitle = 'Website Settings';
require __DIR__ . '/partials/header.php';
?>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
            <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">General</a></li>
            <li class="nav-item"><a class="nav-link" href="#social" data-bs-toggle="tab">Social Media</a></li>
            <li class="nav-item"><a class="nav-link" href="#email" data-bs-toggle="tab">Email</a></li>
        </ul>
    </div>
    <div class="card-body">
        <form method="post" class="tab-content">
            <div class="tab-pane fade show active" id="general">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Store Name</label>
                        <input type="text" name="store_name" class="form-control" value="<?= htmlspecialchars($settings['store_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Store Email</label>
                        <input type="email" name="store_email" class="form-control" value="<?= htmlspecialchars($settings['store_email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="store_phone" class="form-control" value="<?= htmlspecialchars($settings['store_phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Currency</label>
                        <input type="text" name="store_currency" class="form-control" value="<?= htmlspecialchars($settings['store_currency'] ?? 'KES') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="store_address" class="form-control" rows="2"><?= htmlspecialchars($settings['store_address'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Rate (%)</label>
                        <input type="number" step="0.01" name="store_tax_rate" class="form-control" value="<?= htmlspecialchars($settings['store_tax_rate'] ?? '16') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Free Shipping Threshold (<?= htmlspecialchars($settings['store_currency'] ?? 'KES') ?>)</label>
                        <input type="number" step="0.01" name="store_shipping_threshold" class="form-control" value="<?= htmlspecialchars($settings['store_shipping_threshold'] ?? '50000') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Flat Shipping Rate (<?= htmlspecialchars($settings['store_currency'] ?? 'KES') ?>)</label>
                        <input type="number" step="0.01" name="store_shipping_rate" class="form-control" value="<?= htmlspecialchars($settings['store_shipping_rate'] ?? '500') ?>">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="social">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Facebook URL</label>
                        <input type="text" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Twitter URL</label>
                        <input type="text" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram URL</label>
                        <input type="text" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="email">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="email_smtp_host" class="form-control" value="<?= htmlspecialchars($settings['email_smtp_host'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" name="email_smtp_port" class="form-control" value="<?= htmlspecialchars($settings['email_smtp_port'] ?? '587') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="email_smtp_user" class="form-control" value="<?= htmlspecialchars($settings['email_smtp_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="email_smtp_pass" class="form-control" value="<?= htmlspecialchars($settings['email_smtp_pass'] ?? '') ?>">
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
