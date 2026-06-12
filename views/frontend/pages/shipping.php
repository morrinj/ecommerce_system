<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active">Shipping Information</li>
        </ol>
    </nav>
    <h1 class="fw-bold mb-4">Shipping Information</h1>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-truck text-primary me-2"></i>Delivery Options</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Method</th><th>Estimated Time</th><th>Cost</th></tr></thead>
                            <tbody>
                                <tr><td>Standard Shipping</td><td>5-7 business days</td><td><?= SHIPPING_FLAT_RATE > 0 ? formatPrice(SHIPPING_FLAT_RATE) : 'Free' ?></td></tr>
                                <tr><td>Express Shipping</td><td>2-3 business days</td><td><?= formatPrice(SHIPPING_FLAT_RATE * 2) ?></td></tr>
                                <tr><td>Next Day Delivery</td><td>1 business day</td><td><?= formatPrice(SHIPPING_FLAT_RATE * 4) ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>Free standard shipping on orders over <?= formatPrice(SHIPPING_THRESHOLD) ?>!
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-globe text-primary me-2"></i>International Shipping</h5>
                    <p>We ship to over 50 countries worldwide. International delivery typically takes 7-14 business days. Customs fees may apply depending on your country's regulations.</p>
                    <h6 class="fw-bold mt-4">Shipping Policy</h6>
                    <ul>
                        <li>Orders are processed within 1-2 business days</li>
                        <li>Tracking number provided for all orders</li>
                        <li>Signature may be required for delivery</li>
                        <li>PO Box delivery available for standard shipping</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?= url('help') ?>" class="list-group-item list-group-item-action px-0 border-0">Help Center</a>
                        <a href="<?= url('returns') ?>" class="list-group-item list-group-item-action px-0 border-0">Returns Policy</a>
                        <a href="<?= url('contact') ?>" class="list-group-item list-group-item-action px-0 border-0">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
