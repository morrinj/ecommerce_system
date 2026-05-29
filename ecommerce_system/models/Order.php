<?php
require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel {
    protected string $table = 'orders';

    public function __construct() {
        parent::__construct();
    }

    public function createOrder(int $userId, array $shipping, array $billing, ?int $couponId = null, ?string $notes = null): array {
        try {
            $this->beginTransaction();
            $cartModel = new Cart();
            $items = $cartModel->getCart($userId);
            if (empty($items)) {
                throw new Exception('Cart is empty');
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $discountAmount = 0;
            if ($couponId) {
                $couponModel = new Coupon();
                $coupon = $couponModel->find($couponId);
                if ($coupon && $couponModel->isValid($coupon['code'], $userId)) {
                    $discountAmount = $couponModel->calculateDiscount($coupon['code'], $subtotal);
                    $couponModel->applyCoupon($coupon['code'], $userId);
                }
            }

            $taxAmount = round($subtotal * TAX_RATE, 2);
            $shippingAmount = $subtotal >= SHIPPING_THRESHOLD ? 0 : SHIPPING_FLAT_RATE;
            $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

            $orderNumber = generateOrderNumber();
            $orderId = $this->create([
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_id' => $couponId,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'shipping_first_name' => $shipping['first_name'],
                'shipping_last_name'  => $shipping['last_name'],
                'shipping_address1'   => $shipping['address1'],
                'shipping_address2'   => $shipping['address2'] ?? null,
                'shipping_city'       => $shipping['city'],
                'shipping_county'      => $shipping['county'],
                'shipping_zip'        => $shipping['zip'],
                'shipping_country'    => $shipping['country'] ?? 'KE',
                'shipping_phone'      => $shipping['phone'] ?? null,
                'billing_first_name' => $billing['first_name'],
                'billing_last_name'  => $billing['last_name'],
                'billing_address1'   => $billing['address1'],
                'billing_address2'   => $billing['address2'] ?? null,
                'billing_city'       => $billing['city'],
                'billing_county'      => $billing['county'],
                'billing_zip'        => $billing['zip'],
                'billing_country'    => $billing['country'] ?? 'KE',
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_sku, product_image, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    null,
                    $item['image_primary'],
                    $item['price'],
                    $item['quantity'],
                    $item['price'] * $item['quantity']
                ]);

                // Update stock
                $this->db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND is_track_stock = 1")
                    ->execute([$item['quantity'], $item['product_id']]);
            }

            // Log purchases for AI
            foreach ($items as $item) {
                logActivity($userId, 'purchase', $item['product_id']);
            }

            $cartModel->clearCart($userId);
            $this->commit();

            return ['success' => true, 'order_id' => $orderId, 'order_number' => $orderNumber];
        } catch (Exception $e) {
            $this->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getByUser(int $userId, int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByOrderNumber(string $orderNumber): ?array {
        return $this->findBy('order_number', $orderNumber);
    }

    public function getItems(int $orderId): array {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function getWithUser(int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                u.email as customer_email, u.phone as customer_phone,
                CONCAT(COALESCE(o.shipping_address1,''), ', ', COALESCE(o.shipping_city,''), ', ', COALESCE(o.shipping_county,''), ' ', COALESCE(o.shipping_zip,'')) as shipping_address,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $orderId, string $status): bool {
        $data = ['status' => $status];
        if ($status === 'shipped') $data['shipped_at'] = date('Y-m-d H:i:s');
        if ($status === 'delivered') $data['delivered_at'] = date('Y-m-d H:i:s');
        return $this->update($orderId, $data);
    }

    public function updatePaymentStatus(int $orderId, string $status): bool {
        return $this->update($orderId, ['payment_status' => $status]);
    }

    public function getSalesStats(string $period = 'month'): array {
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
        $stmt = $this->db->prepare("SELECT DATE_FORMAT(created_at, '{$format}') as date, SUM(total_amount) as total, COUNT(*) as count
                FROM orders WHERE status NOT IN ('cancelled','refunded') AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
                GROUP BY DATE_FORMAT(created_at, '{$format}') ORDER BY date ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecent(int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                CONCAT(COALESCE(o.shipping_address1,''), ', ', COALESCE(o.shipping_city,''), ', ', COALESCE(o.shipping_county,'')) as shipping_address
                FROM orders o LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRevenueTotal(): float {
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status NOT IN ('cancelled','refunded')");
        return (float) $stmt->fetchColumn();
    }

    public function getOrderCount(): int {
        return $this->count();
    }

    public function getPendingCount(): int {
        return $this->count(['status' => 'pending']);
    }

    public function getRevenueByPeriod(string $start, string $end): float {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status NOT IN ('cancelled','refunded') AND created_at BETWEEN ? AND ?");
        $stmt->execute([$start, $end]);
        return (float) $stmt->fetchColumn();
    }

    public function getOrdersByStatus(string $status, int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name
                FROM orders o LEFT JOIN users u ON o.user_id = u.id
                WHERE o.status = ? ORDER BY o.created_at DESC LIMIT ?");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }

    public function findOrderById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                u.email as customer_email, u.phone as customer_phone,
                CONCAT(COALESCE(o.shipping_address1,''), ', ', COALESCE(o.shipping_city,''), ', ', COALESCE(o.shipping_county,''), ' ', COALESCE(o.shipping_zip,'')) as shipping_address
                FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getTopProducts(int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT p.id, p.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue
                FROM order_items oi JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status NOT IN ('cancelled','refunded')
                GROUP BY p.id, p.name ORDER BY total_sold DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlyStats(int $months = 12): array {
        $stmt = $this->db->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, SUM(total_amount) as revenue
                FROM orders WHERE status NOT IN ('cancelled','refunded') AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC");
        $stmt->bindValue(1, $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStatusBreakdown(): array {
        $stmt = $this->db->query("SELECT status as label, COUNT(*) as value FROM orders GROUP BY status");
        $rows = $stmt->fetchAll();
        $result = ['labels' => [], 'values' => []];
        foreach ($rows as $row) {
            $result['labels'][] = $row['label'];
            $result['values'][] = (int)$row['value'];
        }
        return $result;
    }

    public function getRevenueTrend(int $months = 12): array {
        $stmt = $this->db->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as label, SUM(total_amount) as value
                FROM orders WHERE status NOT IN ('cancelled','refunded') AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY label ASC");
        $stmt->bindValue(1, $months, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $result = ['labels' => [], 'values' => []];
        foreach ($rows as $row) {
            $result['labels'][] = $row['label'];
            $result['values'][] = (float)$row['value'];
        }
        return $result;
    }
}
