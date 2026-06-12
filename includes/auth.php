<?php
/**
 * auth.php - Role-Based Access Control & Password Validation
 *
 * Include this file on every protected page to enforce role-based permissions
 * and validate password strength. Handles Super Admin, Manager, and Customer roles.
 */

// ── Role helpers ──

function hasRole(...$roles): bool {
    return isset($_SESSION['admin_id']) && in_array($_SESSION['admin_role'] ?? '', $roles);
}

function requireRole(...$roles): void {
    if (!isset($_SESSION['admin_id'])) {
        redirect('admin/login');
    }
    if (!in_array($_SESSION['admin_role'] ?? '', $roles)) {
        flash('error', 'You do not have permission to access this page.');
        redirect('admin');
    }
}

function isSuperAdmin(): bool {
    return hasRole('superadmin');
}

function getAdminRole(): ?string {
    return $_SESSION['admin_role'] ?? null;
}

// ── Common password blacklist ──

function getCommonPasswords(): array {
    return [
        'password', 'password123', 'password1234', 'password12345',
        'admin', 'admin123', 'admin1234', 'administrator',
        '12345678', '123456789', '1234567890',
        'qwerty', 'qwerty123', 'qwertyuiop',
        'letmein', 'welcome', 'welcome123',
        'monkey', 'dragon', 'master',
        'abc123', 'abc12345',
        'test', 'test123', 'testing',
        'passw0rd', 'p@ssword', 'P@ssw0rd',
        'root', 'toor',
        'pass123', 'pass1234',
        'manager', 'manager123', 'manager@99',
        'customer', 'customer123', 'customer@99',
        'superadmin', 'superadmin123',
        'shop', 'smartshop', 'smartfashion',
        'Morrin@2026', 'SmartShop#45', 'Admin$Secure2026',
    ];
}

// ── Password validation ──

const PWD_ERR_TOO_SHORT        = 'Password must be at least %d characters long.';
const PWD_ERR_NO_UPPER         = 'Password must contain at least one uppercase letter (A-Z).';
const PWD_ERR_NO_LOWER         = 'Password must contain at least one lowercase letter (a-z).';
const PWD_ERR_NO_NUMBER        = 'Password must contain at least one number (0-9).';
const PWD_ERR_NO_SPECIAL       = 'Password must contain at least one special character (@, #, $, %, &, !, etc.).';
const PWD_ERR_HAS_SPACE        = 'Password must not contain spaces.';
const PWD_ERR_COMMON           = 'This password is too common. Please choose a stronger password.';
const PWD_ERR_MATCHES_EMAIL    = 'Password must not be the same as your email.';
const PWD_ERR_MATCHES_NAME     = 'Password must not be the same as your username or name.';
const PWD_ERR_MISMATCH         = 'Passwords do not match.';

function validatePassword(string $password, int $minLength = 8, ?string $email = null, ?string $name = null): array {
    $errors = [];

    if (strlen($password) < $minLength) {
        $errors[] = sprintf(PWD_ERR_TOO_SHORT, $minLength);
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = PWD_ERR_NO_UPPER;
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = PWD_ERR_NO_LOWER;
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = PWD_ERR_NO_NUMBER;
    }

    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = PWD_ERR_NO_SPECIAL;
    }

    if (preg_match('/\s/', $password)) {
        $errors[] = PWD_ERR_HAS_SPACE;
    }

    $lowerPwd = strtolower($password);
    foreach (getCommonPasswords() as $common) {
        if ($lowerPwd === strtolower($common)) {
            $errors[] = PWD_ERR_COMMON;
            break;
        }
    }

    if ($email !== null && $lowerPwd === strtolower(trim($email))) {
        $errors[] = PWD_ERR_MATCHES_EMAIL;
    }

    if ($name !== null && $lowerPwd === strtolower(trim($name))) {
        $errors[] = PWD_ERR_MATCHES_NAME;
    }

    return $errors;
}

function getPasswordMinLength(): int {
    $role = $_SESSION['admin_role'] ?? 'customer';
    return match ($role) {
        'superadmin' => 12,
        'admin', 'manager' => 10,
        default => 8,
    };
}

// ── Account lockout ──

function isAccountLocked(array $record): bool {
    if (empty($record['locked_until'])) return false;
    return strtotime($record['locked_until']) > time();
}

function getLockoutMinutes(): int {
    return 15;
}

function getMaxFailedAttempts(): int {
    return 5;
}
