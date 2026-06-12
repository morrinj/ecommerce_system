<?php
/**
 * AI Analytics Endpoint
 * 
 * AI-READY ENDPOINT: Replace placeholder logic with ML prediction models.
 * Currently provides basic sales analytics.
 * Ready to integrate with: Prophet, TensorFlow, or any forecasting model.
 */

require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = getDbConnection();
    $period = $_GET['period'] ?? 'month'; // week, month, year

    // Basic sales data
    $salesData = getSalesData($pdo, $period);
    
    // AI READY: Product demand forecasting
    $demandForecast = getDemandForecast($pdo);
    
    // AI READY: Customer segmentation
    $customerSegments = getCustomerSegments($pdo);
    
    // AI READY: Price optimization suggestions
    $priceInsights = getPriceInsights($pdo);

    echo json_encode([
        'success' => true,
        'period' => $period,
        'data' => [
            'sales' => $salesData,
            'demand_forecast' => $demandForecast,
            'customer_segments' => $customerSegments,
            'price_insights' => $priceInsights,
        ],
        'ai_ready' => true,
        'message' => 'Placeholder AI analytics. Replace with ML models for production predictions.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch analytics',
        'error' => APP_ENV === 'development' ? $e->getMessage() : 'Internal server error'
    ]);
}

function getSalesData(PDO $pdo, string $period): array {
    switch ($period) {
        case 'week':
            $interval = '7 DAY';
            $format = '%Y-%m-%d';
            break;
        case 'year':
            $interval = '12 MONTH';
            $format = '%Y-%m';
            break;
        default:
            $interval = '1 MONTH';
            $format = '%Y-%m-%d';
    }
    
    $stmt = $pdo->prepare("
        SELECT DATE_FORMAT(created_at, '{$format}') as date,
               SUM(total_amount) as revenue,
               COUNT(*) as orders,
               AVG(total_amount) as avg_order_value
        FROM orders
        WHERE status NOT IN ('cancelled', 'refunded')
        AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
        GROUP BY DATE_FORMAT(created_at, '{$format}')
        ORDER BY date ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getDemandForecast(PDO $pdo): array {
    // AI READY: Replace with time series forecasting (Prophet, ARIMA, LSTM)
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.stock_quantity,
               COALESCE(SUM(oi.quantity), 0) as units_sold_last_30d,
               CASE 
                   WHEN p.stock_quantity <= p.stock_alert_threshold THEN 'restock_urgent'
                   WHEN p.stock_quantity <= p.stock_alert_threshold * 3 THEN 'restock_soon'
                   ELSE 'sufficient'
               END as stock_status
        FROM products p
        LEFT JOIN order_items oi ON p.id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.id AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        WHERE p.status = 'active' AND p.is_track_stock = 1
        GROUP BY p.id
        ORDER BY units_sold_last_30d DESC
        LIMIT 20
    ");
    return $stmt->fetchAll();
}

function getCustomerSegments(PDO $pdo): array {
    // AI READY: Replace with K-means clustering or RFM analysis
    $stmt = $pdo->query("
        SELECT 
            CASE 
                WHEN total_orders >= 10 AND total_spent >= 1000 THEN 'vip'
                WHEN total_orders >= 5 THEN 'regular'
                WHEN total_orders >= 1 THEN 'new'
                ELSE 'inactive'
            END as segment,
            COUNT(*) as count,
            COALESCE(AVG(total_spent), 0) as avg_spent
        FROM (
            SELECT u.id, 
                   COUNT(o.id) as total_orders,
                   COALESCE(SUM(o.total_amount), 0) as total_spent
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id AND o.status NOT IN ('cancelled', 'refunded')
            GROUP BY u.id
        ) as user_stats
        GROUP BY segment
        ORDER BY avg_spent DESC
    ");
    return $stmt->fetchAll();
}

function getPriceInsights(PDO $pdo): array {
    // AI READY: Replace with dynamic pricing model
    $stmt = $pdo->query("
        SELECT 
            c.name as category,
            AVG(p.price) as avg_price,
            MIN(p.price) as min_price,
            MAX(p.price) as max_price,
            AVG(p.compare_price - p.price) as avg_discount,
            AVG(p.average_rating) as avg_rating,
            COUNT(*) as product_count
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active'
        GROUP BY c.id
        ORDER BY avg_price DESC
    ");
    return $stmt->fetchAll();
}
