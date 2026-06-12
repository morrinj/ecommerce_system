<?php
require_once __DIR__ . '/BaseModel.php';

class Payment extends BaseModel {
    protected string $table = 'payments';

    public function __construct() {
        parent::__construct();
    }

    public function recordPayment(int $orderId, ?int $userId, string $method, float $amount, string $transactionId = null): int {
        return $this->create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'payment_method' => $method,
            'amount' => $amount,
            'status' => 'completed',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getByOrder(int $orderId): array {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
