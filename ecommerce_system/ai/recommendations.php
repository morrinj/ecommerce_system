<?php
/**
 * AI Recommendations Endpoint
 * 
 * AI-READY ENDPOINT: Replace placeholder logic with real ML model integration.
 * 
 * Returns personalized product recommendations based on user behavior.
 * Currently uses rule-based logic (same category, top-rated, etc.)
 * Ready to integrate with: TensorFlow.js, Python microservice, or any ML API.
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

$userId = $_GET['user_id'] ?? null;
$limit = min((int)($_GET['limit'] ?? 8), 20);
$type = $_GET['type'] ?? 'personalized'; // personalized, trending, related, upsell

try {
    $pdo = getDbConnection();
    $recommendations = [];

    switch ($type) {
        case 'personalized':
            if ($userId) {
                // AI READY: Replace with collaborative filtering or content-based model
                // Get categories the user has interacted with
                $stmt = $pdo->prepare("
                    SELECT DISTINCT p.id, p.name, p.slug, p.price, p.compare_price, p.image_primary, p.average_rating, c.name as category_name,
                           COUNT(ua.id) as relevance_score
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN user_activities ua ON ua.product_id = p.id AND ua.user_id = ?
                    WHERE p.status = 'active'
                    GROUP BY p.id
                    ORDER BY relevance_score DESC, p.average_rating DESC
                    LIMIT ?
                ");
                $stmt->execute([$userId, $limit]);
            } else {
                // Guest: show trending/popular
                $stmt = $pdo->prepare("
                    SELECT p.*, c.name as category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'active'
                    ORDER BY p.average_rating DESC, p.review_count DESC
                    LIMIT ?
                ");
                $stmt->execute([$limit]);
            }
            $recommendations = $stmt->fetchAll();
            break;

        case 'trending':
            // AI READY: Replace with real-time trend analysis
            $stmt = $pdo->prepare("
                SELECT p.*, c.name as category_name, COUNT(oi.id) as order_count
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                WHERE p.status = 'active' AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY p.id
                ORDER BY order_count DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $recommendations = $stmt->fetchAll();
            break;

        case 'related':
            $productId = (int)($_GET['product_id'] ?? 0);
            // AI READY: Replace with embedding-based similarity search
            $stmt = $pdo->prepare("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.category_id = (
                    SELECT category_id FROM products WHERE id = ?
                ) AND p.id != ?
                ORDER BY RAND()
                LIMIT ?
            ");
            $stmt->execute([$productId, $productId, $limit]);
            $recommendations = $stmt->fetchAll();
            break;

        case 'upsell':
            $productId = (int)($_GET['product_id'] ?? 0);
            // AI READY: Replace with market basket analysis (association rules)
            $stmt = $pdo->prepare("
                SELECT p.*, c.name as category_name, COUNT(*) as frequency
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                JOIN order_items oi1 ON p.id = oi1.product_id
                JOIN order_items oi2 ON oi1.order_id = oi2.order_id AND oi2.product_id = ?
                WHERE p.status = 'active' AND p.id != ?
                GROUP BY p.id
                ORDER BY frequency DESC
                LIMIT ?
            ");
            $stmt->execute([$productId, $productId, $limit]);
            $recommendations = $stmt->fetchAll();
            break;

        default:
            throw new Exception('Invalid recommendation type');
    }

    echo json_encode([
        'success' => true,
        'type' => $type,
        'count' => count($recommendations),
        'data' => $recommendations,
        'ai_ready' => true,
        'message' => 'Placeholder AI recommendations. Replace with ML model for production.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch recommendations',
        'error' => APP_ENV === 'development' ? $e->getMessage() : 'Internal server error'
    ]);
}
