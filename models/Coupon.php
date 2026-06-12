<?php
require_once __DIR__ . '/BaseModel.php';

class Coupon extends BaseModel {
    protected string $table = 'coupons';

    public function __construct() {
        parent::__construct();
    }

    public function findByCode(string $code): ?array {
        return $this->findBy('code', strtoupper($code));
    }

    public function isValid(string $code, int $userId): array {
        $coupon = $this->findByCode($code);
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }
        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'This coupon is no longer active'];
        }
        $now = date('Y-m-d H:i:s');
        if ($coupon['starts_at'] && $now < $coupon['starts_at']) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid'];
        }
        if ($coupon['expires_at'] && $now > $coupon['expires_at']) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }
        if ($coupon['max_uses'] && $coupon['uses_count'] >= $coupon['max_uses']) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }
        if ($coupon['max_uses_per_user']) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM coupon_usage WHERE coupon_id = ? AND user_id = ?");
            $stmt->execute([$coupon['id'], $userId]);
            if ($stmt->fetchColumn() >= $coupon['max_uses_per_user']) {
                return ['valid' => false, 'message' => 'You have already used this coupon'];
            }
        }
        return ['valid' => true, 'coupon' => $coupon];
    }

    public function calculateDiscount(string $code, float $subtotal): float {
        $coupon = $this->findByCode($code);
        if (!$coupon) return 0;
        if ($coupon['min_order_amount'] > $subtotal) return 0;
        if ($coupon['type'] === 'percentage') {
            return round($subtotal * ($coupon['value'] / 100), 2);
        }
        return min($coupon['value'], $subtotal);
    }

    public function applyCoupon(string $code, int $userId): void {
        $coupon = $this->findByCode($code);
        if (!$coupon) return;
        $this->update($coupon['id'], ['uses_count' => $coupon['uses_count'] + 1]);
        $this->db->prepare("INSERT INTO coupon_usage (coupon_id, user_id) VALUES (?, ?)")
            ->execute([$coupon['id'], $userId]);
    }

    public function getAll(): array {
        return $this->findAll(null, null, 'created_at', 'DESC');
    }
}
