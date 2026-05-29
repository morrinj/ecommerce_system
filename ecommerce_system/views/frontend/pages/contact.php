<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active">Contact Us</li>
        </ol>
    </nav>
    <h1 class="fw-bold mb-4">Contact Us</h1>
    <?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Send Us a Message</h5>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <select name="subject" class="form-select" required>
                                <option value="">Select a topic</option>
                                <option value="order">Order Inquiry</option>
                                <option value="shipping">Shipping Question</option>
                                <option value="return">Return/Refund</option>
                                <option value="product">Product Question</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-5">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Contact Information</h5>
                    <div class="d-flex mb-3">
                        <i class="bi bi-envelope-fill text-primary fs-4 me-3"></i>
                        <div>
                            <strong>Email</strong>
                            <p class="mb-0 text-muted">support@smartshop.co.ke</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="bi bi-telephone-fill text-primary fs-4 me-3"></i>
                        <div>
                            <strong>Phone</strong>
                            <p class="mb-0 text-muted">+254 712 345 678</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt-fill text-primary fs-4 me-3"></i>
                        <div>
                            <strong>Address</strong>
                            <p class="mb-0 text-muted">Kaunda Street<br>Nairobi, Kenya</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-clock-fill text-primary fs-4 me-3"></i>
                        <div>
                            <strong>Business Hours</strong>
                            <p class="mb-0 text-muted">Mon - Fri: 8:00 AM - 5:00 PM EAT<br>Sat: 9:00 AM - 1:00 PM EAT</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-robot display-5 text-primary"></i>
                    <h6 class="fw-bold mt-2">AI Chatbot Available 24/7</h6>
                    <p class="text-muted small">Get instant answers from our AI assistant.</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="document.querySelector('.chatbot-toggle')?.click()">Open Chatbot</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
