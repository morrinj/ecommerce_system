<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin(): void {
        if (isLoggedIn()) redirect('');
        $bgImg = $this->getRandomProductImage();
        require __DIR__ . '/../views/frontend/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('login');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (empty($email) || empty($password)) {
            flash('error', 'Please fill in all fields');
            redirect('login');
        }
        $result = $this->userModel->login($email, $password);
        if ($result['success']) {
            $cartModel = new Cart();
            $cartModel->mergeSessionCart($result['user']['id'], session_id());
            flash('success', 'Welcome back, ' . $result['user']['first_name'] . '!');
            redirect('');
        }
        flash('error', $result['message']);
        redirect('login');
    }

    public function showRegister(): void {
        if (isLoggedIn()) redirect('');
        $bgImg = $this->getRandomProductImage();
        require __DIR__ . '/../views/frontend/auth/register.php';
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('register');
        $data = sanitizeArray($_POST);
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
            flash('error', 'Please fill in all required fields');
            redirect('register');
        }
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        if ($password !== $confirm) {
            flash('error', PWD_ERR_MISMATCH);
            redirect('register');
        }
        $fullName = trim($data['first_name'] . ' ' . $data['last_name']);
        $errors = validatePassword($password, 8, $data['email'] ?? '', $fullName);
        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            redirect('register');
        }
        $result = $this->userModel->register($data);
        if ($result['success']) {
            $this->userModel->login($data['email'], $data['password']);
            flash('success', 'Account created successfully!');
            redirect('');
        }
        flash('error', $result['message']);
        redirect('register');
    }

    public function logout(): void {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
        redirect('login');
    }

    public function showForgotPassword(): void {
        require __DIR__ . '/../views/frontend/auth/forgot_password.php';
    }

    public function forgotPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('forgot-password');
        $email = sanitize($_POST['email'] ?? '');
        if (!validateEmail($email)) {
            flash('error', 'Please enter a valid email address');
            redirect('forgot-password');
        }
        $result = $this->userModel->requestPasswordReset($email);
        flash('success', $result['message']);
        if (isset($result['token'])) {
            $_SESSION['reset_token'] = $result['token'];
        }
        redirect('login');
    }

    public function showResetPassword(string $token): void {
        require __DIR__ . '/../views/frontend/auth/reset_password.php';
    }

    public function resetPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('login');
        $token = sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        if (empty($password) || $password !== $confirm) {
            flash('error', PWD_ERR_MISMATCH);
            redirect('reset-password/' . $token);
        }
        $errors = validatePassword($password, 8);
        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            redirect('reset-password/' . $token);
        }
        $result = $this->userModel->resetPassword($token, $password);
        if ($result['success']) {
            flash('success', $result['message']);
            redirect('login');
        }
        flash('error', $result['message']);
        redirect('login');
    }

    private function getRandomProductImage(): string {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT image_primary FROM products WHERE image_primary IS NOT NULL AND image_primary != '' AND status = 'active' ORDER BY RAND() LIMIT 1");
            $row = $stmt->fetch();
            if ($row && $row['image_primary']) {
                $path = $row['image_primary'];
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }
                return url($path);
            }
        } catch (Exception $e) {}
        return '';
    }
}
