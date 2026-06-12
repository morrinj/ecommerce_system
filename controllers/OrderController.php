<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Coupon.php';
require_once __DIR__ . '/../models/Payment.php';

class OrderController {
    private Order $orderModel;
    private Cart $cartModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
    }

    public function checkout(): void {
        if (!isLoggedIn()) {
            flash('error', 'Please login to checkout');
            redirect('login');
        }
        $userId = getCurrentUserId();
        $items = $this->cartModel->getCart($userId, null);
        if (empty($items)) {
            flash('error', 'Your cart is empty');
            redirect('cart');
        }
        $total = $this->cartModel->getCartTotal($userId, null);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shipping = sanitizeArray($_POST['shipping'] ?? []);
            $billing = sanitizeArray($_POST['billing'] ?? []);
            $sameAsBilling = isset($_POST['same_as_billing']);
            if ($sameAsBilling) {
                $billing = $shipping;
            }
            $couponCode = sanitize($_POST['coupon_code'] ?? '');
            $notes = sanitize($_POST['notes'] ?? '');
            $couponId = null;

            if ($couponCode) {
                $couponModel = new Coupon();
                $coupon = $couponModel->findByCode($couponCode);
                if ($coupon) {
                    $validity = $couponModel->isValid($couponCode, $userId);
                    if ($validity['valid']) {
                        $couponId = $coupon['id'];
                    }
                }
            }

            $result = $this->orderModel->createOrder($userId, $shipping, $billing, $couponId, $notes);
            if ($result['success']) {
                flash('success', 'Order placed successfully! Order #: ' . $result['order_number']);
                redirect('order-confirmation/' . $result['order_number']);
            }
            flash('error', $result['message'] ?? 'Failed to place order');
            redirect('checkout');
        }

        $couponCode = $_GET['coupon'] ?? null;
        $discount = 0;
        if ($couponCode) {
            $couponModel = new Coupon();
            $validity = $couponModel->isValid($couponCode, $userId);
            if ($validity['valid']) {
                $discount = $couponModel->calculateDiscount($couponCode, $total);
            }
        }

        $tax = round($total * TAX_RATE, 2);
        $shipping = $total >= SHIPPING_THRESHOLD ? 0 : SHIPPING_FLAT_RATE;
        $grandTotal = $total - $discount + $tax + $shipping;

        require __DIR__ . '/../views/frontend/checkout/index.php';
    }

    public function confirmation(string $orderNumber): void {
        if (!isLoggedIn()) redirect('login');
        $order = $this->orderModel->getByOrderNumber($orderNumber);
        if (!$order || $order['user_id'] != getCurrentUserId()) {
            flash('error', 'Order not found');
            redirect('');
        }
        $items = $this->orderModel->getItems($order['id']);
        require __DIR__ . '/../views/frontend/checkout/confirmation.php';
    }

    public function track(): void {
        if (!isLoggedIn()) {
            flash('error', 'Please login to view orders');
            redirect('login');
        }
        $orders = $this->orderModel->getByUser(getCurrentUserId());
        require __DIR__ . '/../views/frontend/orders/index.php';
    }

    public function show(string $orderNumber): void {
        if (!isLoggedIn()) redirect('login');
        $order = $this->orderModel->getByOrderNumber($orderNumber);
        if (!$order || $order['user_id'] != getCurrentUserId()) {
            flash('error', 'Order not found');
            redirect('orders');
        }
        $items = $this->orderModel->getItems($order['id']);
        require __DIR__ . '/../views/frontend/orders/show.php';
    }

    public function applyCoupon(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('checkout');
        $code = sanitize($_POST['coupon_code'] ?? '');
        if (empty($code)) {
            flash('error', 'Please enter a coupon code');
            redirect('checkout');
        }
        $couponModel = new Coupon();
        $validity = $couponModel->isValid($code, getCurrentUserId());
        if ($validity['valid']) {
            $_SESSION['coupon_code'] = $code;
            flash('success', 'Coupon applied!');
        } else {
            flash('error', $validity['message']);
        }
        redirect('checkout');
    }
}
