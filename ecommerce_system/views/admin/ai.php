<?php
$pageTitle = 'AI Features';
require __DIR__ . '/partials/header.php';
?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-magic fs-5 text-primary"></i> AI Recommendations
            </div>
            <div class="card-body">
                <p class="text-muted">Manage AI-powered product recommendations shown to customers. These are computed based on browsing history, purchase patterns, and product similarity.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success fs-6 p-2 px-3">Active</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Recommendation Types</label>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Personalized Recommendations
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Trending Products
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Frequently Bought Together
                            <span class="badge bg-secondary rounded-pill">Coming Soon</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Personalized Upsells
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                    </ul>
                </div>
                <a href="<?= url('ai/recommendations.php') ?>" class="btn btn-outline-primary" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View API Endpoint</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-chat-dots fs-5 text-success"></i> AI Chatbot
            </div>
            <div class="card-body">
                <p class="text-muted">The AI-powered customer support chatbot helps visitors find products, track orders, and get instant answers to common questions.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success fs-6 p-2 px-3">Active (Rule-based)</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Chatbot Capabilities</label>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Product Search
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Order Tracking
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            FAQ Responses
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            LLM Integration
                            <span class="badge bg-warning text-dark rounded-pill">Ready</span>
                        </li>
                    </ul>
                </div>
                <a href="<?= url('ai/chatbot.php') ?>" class="btn btn btn-outline-success" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View API Endpoint</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow fs-5 text-warning"></i> AI Analytics
            </div>
            <div class="card-body">
                <p class="text-muted">Machine learning models analyze sales data, customer segments, and demand patterns to provide actionable business insights.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Available Insights</label>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sales Forecasting
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Demand Prediction
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Customer Segmentation
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Price Optimization
                            <span class="badge bg-primary rounded-pill">Active</span>
                        </li>
                    </ul>
                </div>
                <a href="<?= url('ai/analytics.php') ?>" class="btn btn-outline-warning" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View API Endpoint</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-database fs-5 text-info"></i> Training Data
            </div>
            <div class="card-body">
                <p class="text-muted">The AI models are trained on your store data. View statistics about the data available for training.</p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="fs-3 fw-bold"><?= (int)($stats['products'] ?? 0) ?></div>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="fs-3 fw-bold"><?= (int)($stats['orders'] ?? 0) ?></div>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="fs-3 fw-bold"><?= (int)($stats['users'] ?? 0) ?></div>
                            <small class="text-muted">Customers</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="fs-3 fw-bold"><?= (int)($stats['reviews'] ?? 0) ?></div>
                            <small class="text-muted">Reviews</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
