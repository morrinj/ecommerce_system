    </main>
    <footer class="bg-dark text-light">
        <div class="container">
            <div class="row g-5 py-5">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-shop fs-4 me-2"></i><?= APP_NAME ?></h5>
                    <p class="text-muted mb-4" style="line-height: 1.8;">Your premier online fashion destination. Trendy styles, quality fabrics, fast delivery across Kenya.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light text-decoration-none" style="font-size: 1.3rem; opacity: 0.7; transition: opacity 0.2s ease;"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light text-decoration-none" style="font-size: 1.3rem; opacity: 0.7; transition: opacity 0.2s ease;"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-light text-decoration-none" style="font-size: 1.3rem; opacity: 0.7; transition: opacity 0.2s ease;"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-light text-decoration-none" style="font-size: 1.3rem; opacity: 0.7; transition: opacity 0.2s ease;"><i class="bi bi-pinterest"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-4">Shop</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('products') ?>" class="text-muted text-decoration-none">All Products</a></li>
                        <li><a href="<?= url('products?on_sale=1') ?>" class="text-muted text-decoration-none">Sale Items</a></li>
                        <li><a href="<?= url('products?is_new=1') ?>" class="text-muted text-decoration-none">New Arrivals</a></li>
                        <li><a href="<?= url('products?is_featured=1') ?>" class="text-muted text-decoration-none">Featured</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-4">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('help') ?>" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="<?= url('shipping') ?>" class="text-muted text-decoration-none">Shipping Info</a></li>
                        <li><a href="<?= url('returns') ?>" class="text-muted text-decoration-none">Returns</a></li>
                        <li><a href="<?= url('contact') ?>" class="text-muted text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-4">Account</h6>
                    <ul class="list-unstyled">
                        <?php if (isLoggedIn()): ?>
                        <li><a href="<?= url('profile') ?>" class="text-muted text-decoration-none">My Account</a></li>
                        <li><a href="<?= url('orders') ?>" class="text-muted text-decoration-none">My Orders</a></li>
                        <li><a href="<?= url('wishlist') ?>" class="text-muted text-decoration-none">Wishlist</a></li>
                        <?php else: ?>
                        <li><a href="<?= url('login') ?>" class="text-muted text-decoration-none">Login</a></li>
                        <li><a href="<?= url('register') ?>" class="text-muted text-decoration-none">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-4">Contact</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-envelope me-2"></i>support@smartshop.co.ke</li>
                        <li><i class="bi bi-telephone me-2"></i>+254 712 345 678</li>
                        <li><i class="bi bi-geo-alt me-2"></i>Kaunda Street, Nairobi</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary" style="opacity: 0.15;">
            <div class="row align-items-center py-4">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-muted small">&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <img src="<?= placeholderImg(200, 30, 'M-Pesa Visa Mastercard') ?>" alt="Payment Methods" class="img-fluid" style="opacity: 0.6;">
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
