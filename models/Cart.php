<?php
require_once __DIR__ . '/BaseModel.php';

class Cart extends BaseModel {
    protected string $table = 'cart';

    public function __construct() {
        parent::__construct();
    }

    public function getCart(?int $userId = null, ?string $sessionId = null): array {
        if ($userId) {
            $stmt = $this->db->prepare("SELECT c.*, p.name, p.slug, p.price, p.compare_price, p.image_primary, p.stock_quantity, p.status
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = ?
                    ORDER BY c.created_at DESC");
            $stmt->execute([$userId]);
        } elseif ($sessionId) {
            $stmt = $this->db->prepare("SELECT c.*, p.name, p.slug, p.price, p.compare_price, p.image_primary, p.stock_quantity, p.status
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.session_id = ?
                    ORDER BY c.created_at DESC");
            $stmt->execute([$sessionId]);
        } else {
            return [];
        }
        return $stmt->fetchAll();
    }

    public function addItem(int $productId, int $quantity = 1, ?int $userId = null, ?string $sessionId = null): bool {
        $existing = $this->findExisting($productId, $userId, $sessionId);
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            return $this->update($existing['id'], ['quantity' => $newQty]);
        }
        $data = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'user_id' => $userId,
            'session_id' => $sessionId,
        ];
        $this->create($data);
        return true;
    }

    public function updateQuantity(int $cartId, int $quantity): bool {
        if ($quantity <= 0) {
            return $this->delete($cartId);
        }
        return $this->update($cartId, ['quantity' => $quantity]);
    }

    public function removeItem(int $cartId): bool {
        return $this->delete($cartId);
    }

    public function clearCart(?int $userId = null, ?string $sessionId = null): bool {
        if ($userId) {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
            return $stmt->execute([$userId]);
        } elseif ($sessionId) {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE session_id = ?");
            return $stmt->execute([$sessionId]);
        }
        return false;
    }

    public function getCartTotal(?int $userId = null, ?string $sessionId = null): float {
        $items = $this->getCart($userId, $sessionId);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function getCartCount(?int $userId = null, ?string $sessionId = null): int {
        if ($userId) {
            $stmt = $this->db->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
        } elseif ($sessionId) {
            $stmt = $this->db->prepare("SELECT SUM(quantity) FROM cart WHERE session_id = ?");
            $stmt->execute([$sessionId]);
        } else {
            return 0;
        }
        return (int) $stmt->fetchColumn();
    }

    public function mergeSessionCart(int $userId, string $sessionId): void {
        $this->db->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ? AND user_id IS NULL")
            ->execute([$userId, $sessionId]);
    }

    private function findExisting(int $productId, ?int $userId, ?string $sessionId): ?array {
        if ($userId) {
            $stmt = $this->db->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$userId, $productId]);
        } elseif ($sessionId) {
            $stmt = $this->db->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$sessionId, $productId]);
        } else {
            return null;
        }
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
