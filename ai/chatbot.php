<?php
/**
 * AI Chatbot Endpoint
 * 
 * AI-READY ENDPOINT: Replace placeholder logic with real NLP/LLM integration.
 * Currently provides rule-based responses for common queries.
 * Ready to integrate with: OpenAI API, Google Dialogflow, or custom LLM.
 */

require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim(strip_tags($input['message'] ?? ''));
$sessionId = $input['session_id'] ?? session_id();
$userId = getCurrentUserId();

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message is required']);
    exit;
}

try {
    $pdo = getDbConnection();

    // Log the conversation
    $stmt = $pdo->prepare("INSERT INTO chatbot_conversations (user_id, session_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $sessionId, $message]);
    $conversationId = $pdo->lastInsertId();

    // AI READY: Replace this rule-based logic with an LLM API call
    $response = generateChatbotResponse($message, $pdo);

    // Update with response
    $stmt = $pdo->prepare("UPDATE chatbot_conversations SET response = ?, intent = ? WHERE id = ?");
    $stmt->execute([$response['text'], $response['intent'], $conversationId]);

    echo json_encode([
        'success' => true,
        'message' => $response['text'],
        'intent' => $response['intent'],
        'quick_replies' => $response['quick_replies'] ?? [],
        'conversation_id' => (int)$conversationId,
        'ai_ready' => true,
        'note' => 'Placeholder AI chatbot. Replace with LLM integration for production.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, I encountered an error. Please try again.',
        'error' => APP_ENV === 'development' ? $e->getMessage() : 'Internal server error'
    ]);
}

function generateChatbotResponse(string $message, PDO $pdo): array {
    $lower = mb_strtolower($message);

    // Product-related queries
    if (preg_match('/\b(hi|hello|hey|help)\b/', $lower)) {
        return [
            'text' => "Hello! I'm SmartShop AI assistant. I can help you with:\n• Finding products\n• Order status\n• Shipping info\n• Returns & refunds\n• General inquiries\n\nHow can I help you today?",
            'intent' => 'greeting',
            'quick_replies' => ['Show me featured products', 'Track my order', 'Shipping information']
        ];
    }

    if (preg_match('/\b(featured|best.?seller|popular|trending)\b/', $lower)) {
        $stmt = $pdo->query("SELECT name FROM products WHERE is_featured = 1 AND status = 'active' ORDER BY RAND() LIMIT 5");
        $products = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($products) {
            return [
                'text' => "Here are some of our featured products:\n• " . implode("\n• ", $products) . "\n\nWould you like to see more details on any of these?",
                'intent' => 'featured_products',
                'quick_replies' => ['Show all products', 'I need something else']
            ];
        }
    }

    if (preg_match('/\b(order|track|shipping|delivery)\b/', $lower)) {
        if (preg_match('/\b(track|where|status)\b/', $lower)) {
            return [
                'text' => "To track your order, please visit your <a href='" . url('orders') . "'>Order History</a> page. You'll find real-time tracking information there.\n\nIf you need further assistance, please provide your order number.",
                'intent' => 'order_tracking',
                'quick_replies' => ['Go to my orders', 'Contact support']
            ];
        }
        return [
            'text' => "We offer free shipping on orders over " . CURRENCY_SYMBOL . number_format(SHIPPING_THRESHOLD, 0) . "! Standard shipping is " . CURRENCY_SYMBOL . number_format(SHIPPING_FLAT_RATE, 2) . ". Delivery typically takes 3-7 business days.",
            'intent' => 'shipping',
            'quick_replies' => ['Start shopping', 'Check my order']
        ];
    }

    if (preg_match('/\b(return|refund|cancel)\b/', $lower)) {
        return [
            'text' => "We have a 30-day return policy for most items. Items must be unused and in original packaging. Visit our <a href='#'>Returns Center</a> to start a return.\n\nRefunds are processed within 5-7 business days after we receive the item.",
            'intent' => 'returns',
            'quick_replies' => ['Start a return', 'Contact support']
        ];
    }

    if (preg_match('/\b(price|cost|cheap|expensive|discount|sale|coupon)\b/', $lower)) {
        return [
            'text' => "We have great deals available! Check out our <a href='" . url('products?on_sale=1') . "'>Sale Items</a> for current discounts. You can also use coupon codes at checkout for extra savings!",
            'intent' => 'pricing',
            'quick_replies' => ['View sale items', 'Show all products']
        ];
    }

    if (preg_match('/\b(contact|talk|speak|human|agent|representative)\b/', $lower)) {
        return [
            'text' => "You can reach our support team at:\n📧 support@smartshop.co.ke\n📞 +254 712 345 678\n\nOur hours are Monday-Friday, 8AM-5PM EAT.",
            'intent' => 'contact',
            'quick_replies' => ['Send an email', 'Back to help']
        ];
    }

    // AI READY: Fallback to LLM for unknown queries
    return [
        'text' => "I'm not sure I understand. Could you rephrase? I can help with products, orders, shipping, returns, and general inquiries.\n\nOr you can type 'help' to see what I can do.",
        'intent' => 'unknown',
        'quick_replies' => ['Help', 'Show products', 'Contact support']
    ];
}
