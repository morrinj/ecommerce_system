<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class CartController {
    private Cart $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    private function getUserId(): ?int {
        return isLoggedIn() ? getCurrentUserId() : null;
    }

    private function getSessionId(): ?string {
        return isLoggedIn() ? null : session_id();
    }

    public function index(): void {
        $items = $this->cartModel->getCart($this->getUserId(), $this->getSessionId());
        $total = $this->cartModel->getCartTotal($this->getUserId(), $this->getSessionId());
        require __DIR__ . '/../views/frontend/cart/index.php';
    }

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('');
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
            return;
        }
        $this->cartModel->addItem($productId, $quantity, $this->getUserId(), $this->getSessionId());
        $count = $this->cartModel->getCartCount($this->getUserId(), $this->getSessionId());
        logActivity($this->getUserId(), 'add_to_cart', $productId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode(['success' => true, 'count' => $count, 'message' => 'Added to cart']);
            return;
        }
        flash('success', 'Product added to cart!');
        redirect('cart');
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('cart');
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $this->cartModel->updateQuantity($cartId, $quantity);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $total = $this->cartModel->getCartTotal($this->getUserId(), $this->getSessionId());
            echo json_encode(['success' => true, 'total' => $total]);
            return;
        }
        redirect('cart');
    }

    public function remove(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('cart');
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $this->cartModel->removeItem($cartId);
        flash('success', 'Item removed from cart');
        redirect('cart');
    }

    public function count(): void {
        $count = $this->cartModel->getCartCount($this->getUserId(), $this->getSessionId());
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
    }
}
