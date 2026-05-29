<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Wishlist.php';

class ProfileController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function dashboard(): void {
        if (!isLoggedIn()) redirect('login');
        $user = $this->userModel->find(getCurrentUserId());
        $orderModel = new Order();
        $recentOrders = $orderModel->getByUser(getCurrentUserId(), 5);
        require __DIR__ . '/../views/frontend/profile/dashboard.php';
    }

    public function edit(): void {
        if (!isLoggedIn()) redirect('login');
        $user = $this->userModel->find(getCurrentUserId());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $this->userModel->updateProfile(getCurrentUserId(), $data);
            flash('success', 'Profile updated successfully');
            redirect('profile');
        }
        require __DIR__ . '/../views/frontend/profile/edit.php';
    }

    public function password(): void {
        if (!isLoggedIn()) redirect('login');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if ($new !== $confirm || strlen($new) < 6) {
                flash('error', 'Passwords do not match or too short');
                redirect('profile/password');
            }
            $result = $this->userModel->changePassword(getCurrentUserId(), $current, $new);
            if ($result['success']) {
                flash('success', $result['message']);
            } else {
                flash('error', $result['message']);
            }
            redirect('profile/password');
        }
        require __DIR__ . '/../views/frontend/profile/password.php';
    }

    public function wishlist(): void {
        if (!isLoggedIn()) redirect('login');
        $wishlistModel = new Wishlist();
        $items = $wishlistModel->getUserWishlist(getCurrentUserId());
        require __DIR__ . '/../views/frontend/profile/wishlist.php';
    }

    public function toggleWishlist(): void {
        if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Please login']);
            return;
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $wishlist = new Wishlist();
        $result = $wishlist->toggle(getCurrentUserId(), $productId);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
