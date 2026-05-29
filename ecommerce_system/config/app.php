<?php
session_start();

define('APP_NAME', 'SmartFashion');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('APP_URL', getenv('APP_URL') ?: "{$scheme}://{$host}{$scriptDir}");
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('ITEMS_PER_PAGE', 12);
define('CURRENCY_SYMBOL', 'KSh ');
define('CURRENCY_CODE', 'KES');
define('TAX_RATE', 0.16);
define('SHIPPING_THRESHOLD', 50000.00);
define('SHIPPING_FLAT_RATE', 500.00);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

function redirect(string $path): void {
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

function asset(string $path): string {
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function url(string $path = ''): string {
    return APP_URL . '/' . ltrim($path, '/');
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['admin_id']);
}

function getCurrentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentAdminId(): ?int {
    return $_SESSION['admin_id'] ?? null;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function old(string $key, $default = '') {
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, ?string $value = null) {
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return;
    }
    $val = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $val;
}

function hasFlash(string $key): bool {
    return isset($_SESSION['_flash'][$key]);
}

function error(string $key, ?string $value = null) {
    if ($value !== null) {
        $_SESSION['_errors'][$key] = $value;
        return;
    }
    $val = $_SESSION['_errors'][$key] ?? null;
    unset($_SESSION['_errors'][$key]);
    return $val;
}

function hasError(string $key): bool {
    return isset($_SESSION['_errors'][$key]);
}
