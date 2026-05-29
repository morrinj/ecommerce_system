<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected string $table = 'users';

    public function __construct() {
        parent::__construct();
    }

    public function register(array $data): array {
        if (!validateEmail($data['email'])) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        $existing = $this->findBy('email', $data['email']);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $userId = $this->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'phone'      => $data['phone'] ?? null,
        ]);
        return ['success' => true, 'user_id' => $userId];
    }

    public function login(string $email, string $password): array {
        $user = $this->findBy('email', $email);
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is ' . $user['status']];
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        return ['success' => true, 'user' => $user];
    }

    public function updateProfile(int $userId, array $data): bool {
        $allowed = ['first_name','last_name','phone','address_line1','address_line2','city','county','zip_code','country','avatar'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) return false;
        return $this->update($userId, $update);
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
        $user = $this->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        $this->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
        return ['success' => true, 'message' => 'Password changed successfully'];
    }

    public function requestPasswordReset(string $email): array {
        $user = $this->findBy('email', $email);
        if (!$user) {
            return ['success' => false, 'message' => 'If that email exists, a reset link has been sent'];
        }
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires' => $expires
        ]);
        // In production, send email here
        return ['success' => true, 'message' => 'Password reset link sent to your email', 'token' => $token];
    }

    public function resetPassword(string $token, string $password): array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid or expired reset token'];
        }
        $this->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null
        ]);
        return ['success' => true, 'message' => 'Password has been reset successfully'];
    }

    public function getOrders(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getCount(): int {
        return $this->count(['status' => 'active']);
    }

    public function getAll(int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT u.*, CONCAT(u.first_name, ' ', u.last_name) as full_name,
                (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND status NOT IN ('cancelled','refunded')) as total_spent
                FROM users u ORDER BY u.created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $userId, string $status): bool {
        return $this->update($userId, ['status' => $status]);
    }
}
