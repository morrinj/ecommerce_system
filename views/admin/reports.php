<?php
/** @var float $totalRevenue */
/** @var int $totalOrders */
/** @var float $avgOrderValue */
/** @var float $conversionRate */
/** @var array $revenueTrend */
/** @var array $statusData */
/** @var array $topProducts */
/** @var array $monthlyData */
$pageTitle = 'Reports & Analytics';
require __DIR__ . '/partials/header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="label">Total Revenue</div>
            <div class="value text-success"><?= formatPrice($totalRevenue ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="label">Total Orders</div>
            <div class="value"><?= $totalOrders ?? 0 ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="label">Avg Order Value</div>
            <div class="value text-info"><?= formatPrice($avgOrderValue ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="label">Conversion Rate</div>
            <div class="value text-primary"><?= ($conversionRate ?? 0) ?>%</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Revenue Trend</div>
            <div class="card-body">
                <canvas id="revenueChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Orders by Status</div>
            <div class="card-body">
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Top Products</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                    <tbody>
                        <?php if (!empty($topProducts)): foreach ($topProducts as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= (int)$p['total_sold'] ?></td>
                            <td><?= formatPrice($p['revenue'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="text-muted">No data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Orders by Month</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th></tr></thead>
                    <tbody>
                        <?php if (!empty($monthlyData)): foreach ($monthlyData as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['month']) ?></td>
                            <td><?= (int)$m['count'] ?></td>
                            <td><?= formatPrice($m['revenue'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="text-muted">No data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        const revData = <?= json_encode($revenueTrend ?? ['labels' => [], 'values' => []]) ?>;
        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: revData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: revData.values,
                    backgroundColor: 'rgba(13,110,253,0.6)',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 4
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
    }

    const stCtx = document.getElementById('statusChart');
    if (stCtx) {
        const stData = <?= json_encode($statusData ?? ['labels' => [], 'values' => []]) ?>;
        new Chart(stCtx, {
            type: 'doughnut',
            data: {
                labels: stData.labels,
                datasets: [{
                    data: stData.values,
                    backgroundColor: ['#ffc107', '#0d6efd', '#198754', '#6c757d', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
