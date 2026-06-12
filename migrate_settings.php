<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDbConnection();

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT DEFAULT NULL,
        group_name VARCHAR(50) DEFAULT 'general',
        type ENUM('text','textarea','email','image','select','number') DEFAULT 'text',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_settings_key (setting_key),
        INDEX idx_settings_group (group_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO settings (setting_key, setting_value, group_name, type) VALUES
            ('store_name', 'SmartShop', 'general', 'text'),
            ('store_email', 'admin@smartshop.com', 'general', 'email'),
            ('store_phone', '+254 712 345 678', 'general', 'text'),
            ('store_address', 'Kaunda Street, Nairobi, Kenya', 'general', 'textarea'),
            ('store_currency', 'KES', 'general', 'text'),
            ('store_tax_rate', '16', 'general', 'number'),
            ('store_shipping_threshold', '50000', 'general', 'number'),
            ('store_shipping_rate', '500', 'general', 'number'),
            ('facebook_url', '', 'social', 'text'),
            ('twitter_url', '', 'social', 'text'),
            ('instagram_url', '', 'social', 'text'),
            ('email_smtp_host', '', 'email', 'text'),
            ('email_smtp_port', '587', 'email', 'number'),
            ('email_smtp_user', '', 'email', 'text'),
            ('email_smtp_pass', '', 'email', 'text')");
    }

    echo "Settings table created and seeded successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
