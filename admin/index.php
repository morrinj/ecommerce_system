<?php
// This file should rarely be hit — .htaccess routes all /admin/* URLs
// through the main index.php. This is only a fallback.
require_once __DIR__ . '/../config/app.php';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base = rtrim($scheme . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
if (isAdmin()) {
    header('Location: ' . $base . '/admin/dashboard');
    exit;
}
header('Location: ' . $base . '/admin/login');
exit;
