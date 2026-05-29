<?php
require_once __DIR__ . '/BaseModel.php';

class Admin extends BaseModel {
    protected string $table = 'admins';

    public function __construct() {
        parent::__construct();
    }

    public function login(string $email, string $password): array {
        $admin = $this->findBy('email', $email);
        if (!$admin || !password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        if ($admin['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is inactive'];
        }
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];
        $this->update($admin['id'], ['last_login' => date('Y-m-d H:i:s')]);
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
