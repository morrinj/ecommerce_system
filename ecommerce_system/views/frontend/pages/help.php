<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active">Help Center</li>
        </ol>
    </nav>
    <h1 class="fw-bold mb-4">Help Center</h1>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="accordion" id="helpAccordion">
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header"><button class="accordion-button fw-semibold" data-bs-toggle="collapse" data-bs-target="#help1">How do I place an order?</button></h2>
                    <div id="help1" class="accordion-collapse collapse show" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">Browse products, add items to your cart, proceed to checkout, enter shipping details, and confirm your order. You'll receive a confirmation email with your order number.</div>
                    </div>
                </div>
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header"><button class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#help2">What payment methods do you accept?</button></h2>
                    <div id="help2" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">We accept credit/debit cards (Visa, MasterCard, Amex), PayPal, bank transfers, and cash on delivery.</div>
                    </div>
                </div>
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header"><button class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#help3">How can I track my order?</button></h2>
                    <div id="help3" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">Log in to your account and visit <a href="<?= url('orders') ?>">My Orders</a> to see real-time tracking information for all your orders.</div>
                    </div>
                </div>
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header"><button class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#help4">How do I reset my password?</button></h2>
                    <div id="help4" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">Click "Forgot Password" on the login page, enter your email, and we'll send you a password reset link.</div>
                    </div>
                </div>
                <div class="accordion-item border-0 shadow-sm">
                    <h2 class="accordion-header"><button class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#help5">Can I change or cancel my order?</button></h2>
                    <div id="help5" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">You can cancel or modify your order within 1 hour of placing it. Contact our support team for assistance.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-headset display-4 text-primary"></i>
                    <h5 class="fw-bold mt-3">Still Need Help?</h5>
                    <p class="text-muted">Our support team is ready to assist you.</p>
                    <a href="<?= url('contact') ?>" class="btn btn-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
