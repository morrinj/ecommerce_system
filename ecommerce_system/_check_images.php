<?php
require __DIR__ . '/config/database.php';
$pdo = getDbConnection();
$stmt = $pdo->query('SELECT id, name, image_primary FROM products LIMIT 30');
foreach ($stmt as $r) {
    echo $r['id'] . ' | ' . mb_substr($r['name'], 0, 40) . ' | ' . mb_substr($r['image_primary'], 0, 120) . PHP_EOL;
}
echo PHP_EOL . 'Total products: ' . $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() . PHP_EOL;
echo 'With NULL images: ' . $pdo->query('SELECT COUNT(*) FROM products WHERE image_primary IS NULL OR image_primary = ""')->fetchColumn() . PHP_EOL;
echo 'With myntra paths: ' . $pdo->query("SELECT COUNT(*) FROM products WHERE image_primary LIKE '%myntradataset%'")->fetchColumn() . PHP_EOL;
echo 'With http URLs: ' . $pdo->query("SELECT COUNT(*) FROM products WHERE image_primary LIKE 'http%'")->fetchColumn() . PHP_EOL;
