<?php
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeArray(array $data): array {
    $cleaned = [];
    foreach ($data as $key => $value) {
        $cleaned[$key] = is_string($value) ? sanitize($value) : $value;
    }
    return $cleaned;
}

function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function formatPrice(float $amount): string {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function formatPriceNoDecimal(float $amount): string {
    return CURRENCY_SYMBOL . number_format($amount, 0);
}

function placeholderImg(int $w = 300, int $h = 300, string $text = 'No Image'): string {
    return APP_URL . '/assets/placeholder.php?w=' . $w . '&h=' . $h . '&text=' . urlencode($text);
}

function productImg(?string $path, string $name, int $w = 300, int $h = 300): string {
    if ($path) {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_contains($path, 'myntradataset')) {
            return url('assets/img.php?src=' . urlencode($path) . '&w=' . $w . '&h=' . $h);
        }
        return url($path);
    }
    return placeholderImg($w, $h, htmlspecialchars($name));
}

function truncate(string $text, int $length = 100): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

function timeAgo(string $datetime): string {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $timestamp);
}

function generateOrderNumber(): string {
    return 'ORD-' . strtoupper(uniqid()) . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

function getStatusBadge(string $status): string {
    $map = [
        'pending'    => 'warning',
        'processing' => 'info',
        'shipped'    => 'primary',
        'delivered'  => 'success',
        'cancelled'  => 'danger',
        'refunded'   => 'secondary',
        'active'     => 'success',
        'inactive'   => 'secondary',
        'completed'  => 'success',
        'failed'     => 'danger',
        'draft'      => 'secondary',
    ];
    $class = $map[$status] ?? 'secondary';
    return "<span class=\"badge bg-{$class}\">{$status}</span>";
}

function logActivity(int $userId = null, string $type, int $productId = null, int $categoryId = null, string $searchQuery = null, array $metadata = null): void {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, session_id, activity_type, product_id, category_id, search_query, metadata) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            session_id(),
            $type,
            $productId,
            $categoryId,
            $searchQuery,
            $metadata ? json_encode($metadata) : null
        ]);
    } catch (Exception $e) {
        // Silently log activity failures
    }
}
