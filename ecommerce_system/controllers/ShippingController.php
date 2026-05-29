<?php
require_once __DIR__ . '/../models/Shipping.php';
require_once __DIR__ . '/../models/Cart.php';

class ShippingController {
    private Shipping $shippingModel;
    private Cart $cartModel;

    public function __construct() {
        $this->shippingModel = new Shipping();
        $this->cartModel = new Cart();
    }

    public function index(): void {
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue checkout');
            redirect('login');
        }

        $userId = getCurrentUserId();
        $items = $this->cartModel->getCart($userId, null);

        if (empty($items)) {
            flash('error', 'Your cart is empty');
            redirect('cart');
        }

        $subtotal = $this->cartModel->getCartTotal($userId, null);
        $shipping = Shipping::calculateShipping($subtotal);
        $deliveryOptions = Shipping::getDeliveryOptions();
        $counties = Shipping::getKenyanCounties();
        $savedShipping = $this->shippingModel->getByUser($userId);
        $aiPlaceholders = $this->shippingModel->prepareForAI($savedShipping ?? []);

        require __DIR__ . '/../views/frontend/checkout/shipping.php';
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('checkout/shipping');
        }

        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect('login');
        }

        $userId = getCurrentUserId();

        $rules = [
            'full_name'       => 'required|max:100',
            'email'           => 'required|email',
            'phone'           => 'required|max:20',
            'county'          => 'required',
            'city'            => 'required|max:100',
            'address'         => 'required',
            'delivery_option' => 'required',
        ];

        $errors = $this->validate($_POST, $rules);

        if (!empty($errors)) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old'] = $_POST;
            flash('error', 'Please fix the errors below');
            redirect('checkout/shipping');
            return;
        }

        $data = [
            'full_name'       => sanitize($_POST['full_name'] ?? ''),
            'email'           => sanitize($_POST['email'] ?? ''),
            'phone'           => sanitize($_POST['phone'] ?? ''),
            'county'          => sanitize($_POST['county'] ?? ''),
            'city'            => sanitize($_POST['city'] ?? ''),
            'address'         => sanitize($_POST['address'] ?? ''),
            'apartment'       => sanitize($_POST['apartment'] ?? ''),
            'postal_code'     => sanitize($_POST['postal_code'] ?? ''),
            'delivery_option' => sanitize($_POST['delivery_option'] ?? 'standard'),
            'order_notes'     => sanitize($_POST['order_notes'] ?? ''),
        ];

        $subtotal = $this->cartModel->getCartTotal($userId, null);
        $shipping = Shipping::calculateShipping($subtotal);

        $this->shippingModel->saveDetails($userId, $data);

        $_SESSION['shipping_details'] = $data;
        $_SESSION['shipping_cost'] = $shipping['cost'];

        flash('success', 'Shipping information saved!');
        redirect('checkout');
    }

    public function calculateAjax(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $userId = getCurrentUserId();
        $subtotal = $this->cartModel->getCartTotal($userId, null);
        $shipping = Shipping::calculateShipping($subtotal);

        echo json_encode([
            'subtotal'          => $subtotal,
            'shipping_cost'     => $shipping['cost'],
            'is_free_shipping'  => $shipping['is_free'],
            'shipping_label'    => $shipping['label'],
            'remaining'         => $shipping['remaining'],
            'threshold'         => $shipping['threshold'],
            'formatted_subtotal' => formatPrice($subtotal),
        ]);
    }

    private function validate(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $parts = explode('|', $ruleSet);
            foreach ($parts as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $label = ucfirst(str_replace('_', ' ', $field));
                    $errors[$field] = "$label is required";
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) explode(':', $rule)[1];
                    if (strlen($data[$field] ?? '') > $max) {
                        $label = ucfirst(str_replace('_', ' ', $field));
                        $errors[$field] = "$label must not exceed $max characters";
                    }
                }
                if ($rule === 'email' && !validateEmail($data[$field] ?? '')) {
                    $errors[$field] = 'Please enter a valid email address';
                }
            }
        }
        return $errors;
    }
}
