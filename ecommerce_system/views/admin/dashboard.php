<?php $pageTitle = 'Dashboard'; require __DIR__ . '/partials/header.php'; ?>

<div class="row g-4 mb-4">
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-currency-exchange"></i></div>
            <div>
                <div class="value"><?= formatPrice($totalRevenue ?? 0) ?></div>
                <div class="label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-cart-check"></i></div>
            <div>
                <div class="value"><?= $totalOrders ?? 0 ?></div>
                <div class="label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass"></i></div>
            <div>
                <div class="value"><?= $pendingOrders ?? 0 ?></div>
                <div class="label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-people"></i></div>
            <div>
                <div class="value"><?= $totalUsers ?? 0 ?></div>
                <div class="label">Customers</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-box"></i></div>
            <div>
                <div class="value"><?= $totalProducts ?? 0 ?></div>
                <div class="label">Products</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Sales Overview</span>
                <div class="btn-group btn-group-sm">
                    <a href="?range=7days" class="btn btn-outline-secondary <?= ($_GET['range'] ?? 'month') === '7days' ? 'active' : '' ?>">7D</a>
                    <a href="?range=month" class="btn btn-outline-secondary <?= ($_GET['range'] ?? 'month') === 'month' ? 'active' : '' ?>">30D</a>
                    <a href="?range=year" class="btn btn-outline-secondary <?= ($_GET['range'] ?? 'month') === 'year' ? 'active' : '' ?>">12M</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Low Stock Alert</div>
            <div class="card-body p-0">
                <?php if (!empty($lowStock)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($lowStock as $item): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="text-truncate me-2">
                            <a href="<?= url('admin/products/edit/' . $item['id']) ?>" class="text-decoration-none"><?= htmlspecialchars($item['name']) ?></a>
                            <small class="d-block text-danger">Stock: <?= (int)$item['stock_quantity'] ?></small>
                        </div>
                        <span class="badge bg-danger rounded-pill"><?= (int)$item['stock_quantity'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">All items well stocked.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Pending Reviews</div>
            <div class="card-body p-0">
                <?php if (!empty($pendingReviews)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pendingReviews as $review): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= htmlspecialchars($review['user_name'] ?? 'Anonymous') ?></strong>
                                <span class="text-warning ms-2"><?= str_repeat('★', (int)$review['rating']) ?></span>
                            </div>
                            <small class="text-muted"><?= date('M d', strtotime($review['created_at'])) ?></small>
                        </div>
                        <p class="mb-0 small text-muted"><?= htmlspecialchars($review['comment']) ?></p>
                        <div class="mt-1">
                            <a href="<?= url('admin/reviews/approve/' . $review['id']) ?>" class="btn btn-sm btn-success">Approve</a>
                            <a href="<?= url('admin/reviews/delete/' . $review['id']) ?>" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">No pending reviews.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Top Selling Products</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($topSelling)): ?>
                <table class="table mb-0">
                    <thead><tr><th>#</th><th>Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                    <tbody>
                        <?php $i = 1; foreach ($topSelling as $p): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><a href="<?= url('admin/products/edit/' . $p['id']) ?>" class="text-decoration-none"><?= htmlspecialchars($p['name']) ?></a></td>
                            <td><?= (int)$p['total_sold'] ?></td>
                            <td><?= formatPrice($p['revenue'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">No sales data yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Orders</span>
                <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentOrders)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="<?= url('admin/orders/view/' . $order['id']) ?>" class="text-decoration-none fw-semibold">#<?= $order['order_number'] ?></a>
                                <small class="text-muted ms-2"><?= htmlspecialchars($order['customer_name']) ?></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : ($order['status'] === 'shipped' ? 'info' : 'warning')) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                                <strong><?= formatPrice((float)($order['total_amount'] ?? 0)) ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">No orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    const salesData = <?= json_encode($salesData ?? ['labels' => [], 'values' => []]) ?>;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: 'Sales',
                data: salesData.values,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
