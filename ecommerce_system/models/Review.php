<?php
require_once __DIR__ . '/BaseModel.php';

class Review extends BaseModel {
    protected string $table = 'reviews';

    public function __construct() {
        parent::__construct();
    }

    public function getByProduct(int $productId, int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ? AND r.is_approved = 1
                ORDER BY r.created_at DESC LIMIT ?");
        $stmt->execute([$productId, $limit]);
        return $stmt->fetchAll();
    }

    public function addReview(int $productId, ?int $userId, int $rating, string $title = null, string $comment = null, ?int $orderId = null): int {
        return $this->create([
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'is_approved' => 0,
        ]);
    }

    public function approve(int $reviewId): bool {
        $result = $this->update($reviewId, ['is_approved' => 1]);
        if ($result) {
            $review = $this->find($reviewId);
            if ($review) {
                $this->recalculateProductRating($review['product_id']);
            }
        }
        return $result;
    }

    public function getPending(): array {
        $stmt = $this->db->query("SELECT r.*, p.name as product_name, CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM reviews r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.is_approved = 0
                ORDER BY r.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getStats(int $productId): array {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total, AVG(rating) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one
                FROM reviews WHERE product_id = ? AND is_approved = 1");
        $stmt->execute([$productId]);
        return $stmt->fetch() ?: [];
    }

    public function recalculateProductRating(int $productId): void {
        $stats = $this->getStats($productId);
        $avg = $stats['avg_rating'] ?? 0;
        $count = $stats['total'] ?? 0;
        $stmt = $this->db->prepare("UPDATE products SET average_rating = ?, review_count = ? WHERE id = ?");
        $stmt->execute([round($avg, 2), $count, $productId]);
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT r.*, p.name as product_name, CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM reviews r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getCount(): int {
        return $this->count();
    }

    public function hasReviewed(int $productId, int $userId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$productId, $userId]);
        return $stmt->fetchColumn() > 0;
    }
}
