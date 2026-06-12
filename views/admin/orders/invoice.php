<?php
/** @var array $order */
/** @var array $items */
$pageTitle = 'Invoice #' . $order['order_number']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $order['order_number'] ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; padding: 40px; color: #333; }
        .invoice-header { border-bottom: 2px solid #0d6efd; padding-bottom: 20px; margin-bottom: 30px; }
        .table th { background: #f8f9fa; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="no-print text-end mb-3">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i>Print</button>
        <button onclick="window.close()" class="btn btn-outline-secondary">Close</button>
    </div>

    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1"><?= APP_NAME ?></h2>
            <small class="text-muted">Invoice</small>
        </div>
        <div class="text-end">
            <h4 class="mb-1">#<?= $order['order_number'] ?></h4>
            <small class="text-muted">Date: <?= date('F d, Y', strtotime($order['created_at'])) ?></small>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <strong>Bill To:</strong>
            <p class="mb-0"><?= htmlspecialchars($order['customer_name']) ?><br>
            <?= nl2br(htmlspecialchars($order['shipping_address'] ?? 'N/A')) ?><br>
            <?= htmlspecialchars($order['customer_email'] ?? '') ?><br>
            <?= htmlspecialchars($order['customer_phone'] ?? '') ?></p>
        </div>
        <div class="col-6 text-end">
            <strong>Payment Method:</strong>
            <p class="mb-0"><?= ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')) ?><br>
            Status: <span class="text-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>"><?= ucfirst($order['payment_status']) ?></span></p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead><tr><th>#</th><th>Product</th><th class="text-end">Price</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr></thead>
        <tbody>
            <?php $i = 1; foreach ($items as $item): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-end"><?= formatPrice($item['price']) ?></td>
                <td class="text-center"><?= (int)$item['quantity'] ?></td>
                <td class="text-end"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end"><?= formatPrice($order['subtotal']) ?></td></tr>
            <tr><td colspan="4" class="text-end">Shipping</td><td class="text-end"><?= formatPrice($order['shipping_amount']) ?></td></tr>
            <?php if ($order['tax_amount'] > 0): ?>
            <tr><td colspan="4" class="text-end">Tax</td><td class="text-end"><?= formatPrice($order['tax_amount']) ?></td></tr>
            <?php endif; ?>
            <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end"><?= formatPrice($order['total_amount']) ?></td></tr>
        </tfoot>
    </table>

    <p class="text-muted text-center mt-4 small">Thank you for your business!</p>
</body>
</html>
