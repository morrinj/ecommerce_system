<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active">Returns & Exchanges</li>
        </ol>
    </nav>
    <h1 class="fw-bold mb-4">Returns & Exchanges</h1>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-arrow-return-left text-primary me-2"></i>30-Day Return Policy</h5>
                    <p>We want you to love your purchase! If you're not completely satisfied, you can return most items within 30 days of delivery for a full refund or exchange.</p>
                    <h6 class="fw-bold mt-4">Conditions</h6>
                    <ul>
                        <li>Items must be unused and in original packaging</li>
                        <li>Tags and labels must still be attached</li>
                        <li>Proof of purchase required</li>
                        <li>Sale items are final sale unless defective</li>
                    </ul>
                    <h6 class="fw-bold mt-4">How to Return</h6>
                    <ol>
                        <li>Log in to your account and go to <a href="<?= url('orders') ?>">My Orders</a></li>
                        <li>Select the order and click "Return Items"</li>
                        <li>Print the prepaid return label</li>
                        <li>Pack items securely and drop off at any carrier location</li>
                    </ol>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-currency-exchange text-primary me-2"></i>Refund Timeline</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Method</th><th>Processing Time</th></tr></thead>
                            <tbody>
                                <tr><td>Credit Card</td><td>5-7 business days</td></tr>
                                <tr><td>PayPal</td><td>24-48 hours</td></tr>
                                <tr><td>Bank Transfer</td><td>7-10 business days</td></tr>
                                <tr><td>Cash on Delivery</td><td>Store credit within 24 hours</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Have Questions?</h6>
                    <p class="text-muted">Our support team can help with your return.</p>
                    <a href="<?= url('contact') ?>" class="btn btn-primary w-100">Contact Support</a>
                    <hr>
                    <small class="text-muted">Need help ASAP? Try our <a href="#" onclick="document.querySelector('.chatbot-toggle')?.click();return false;">AI Chatbot</a>.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
