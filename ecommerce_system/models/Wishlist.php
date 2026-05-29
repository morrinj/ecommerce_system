<?php
require_once __DIR__ . '/BaseModel.php';

class Wishlist extends BaseModel {
    protected string $table = 'wishlist';

    public function __construct() {
        parent::__construct();
    }

    public function toggle(int $userId, int $productId): array {
        $existing = $this->rawSingle("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'removed', 'message' => 'Removed from wishlist'];
        }
        $this->create(['user_id' => $userId, 'product_id' => $productId]);
        logActivity($userId, 'wishlist', $productId);
        return ['action' => 'added', 'message' => 'Added to wishlist'];
    }

    public function getUserWishlist(int $userId): array {
        $stmt = $this->db->prepare("SELECT w.id as wishlist_id, w.created_at as added_at, p.*, c.name as category_name
                FROM wishlist w
                JOIN products p ON w.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE w.user_id = ? AND p.status = 'active'
                ORDER BY w.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function isInWishlist(int $userId, int $productId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getWishlistIds(int $userId): array {
        $stmt = $this->db->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(), 'product_id');
    }
}
