<?php
require_once __DIR__ . '/BaseModel.php';

class Admin extends BaseModel {
    protected string $table = 'admins';

    public function __construct() {
        parent::__construct();
    }

    public function login(string $email, string $password): array {
        $admin = $this->findBy('email', $email);

        if ($admin && isAccountLocked($admin)) {
            $until = date('H:i', strtotime($admin['locked_until']));
            return ['success' => false, 'message' => "Account locked until {$until}. Too many failed attempts."];
        }

        if (!$admin || !password_verify($password, $admin['password'])) {
            if ($admin) {
                $attempts = (int)($admin['failed_attempts'] ?? 0) + 1;
                $update = ['failed_attempts' => $attempts];
                if ($attempts >= getMaxFailedAttempts()) {
                    $update['locked_until'] = date('Y-m-d H:i:s', strtotime('+' . getLockoutMinutes() . ' minutes'));
                }
                $this->update($admin['id'], $update);
            }
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if ($admin['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is inactive'];
        }

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];
        $this->update($admin['id'], [
            'last_login' => date('Y-m-d H:i:s'),
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        if ($admin['role'] === 'superadmin' && !empty($admin['password_changed_at'])) {
            $changed = strtotime($admin['password_changed_at']);
            if (time() - $changed > 90 * 86400) {
                $_SESSION['force_password_change'] = true;
                return ['success' => true, 'admin' => $admin, 'force_change' => true];
            }
        }

        return ['success' => true, 'admin' => $admin];
    }

    public function getAllAdmins(): array {
        return $this->findAll(null, null, 'created_at', 'DESC');
    }

    public function getAll(): array {
        return $this->findAll(null, null, 'created_at', 'DESC');
    }

    public function createAdmin(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }
}
