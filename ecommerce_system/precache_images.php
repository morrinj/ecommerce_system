<?php
require_once __DIR__ . '/config/database.php';

echo "=== Pre-cache ASOS Product Images ===\n\n";

try {
    $pdo = getDbConnection();

    // Get products
    $stmt = $pdo->query("SELECT id, name, sku, image_primary FROM products WHERE image_primary IS NOT NULL AND image_primary != '' AND status = 'active' ORDER BY RAND() LIMIT 100");
    $products = $stmt->fetchAll();

    echo "Found " . count($products) . " products to cache.\n";

    $cached = 0;
    $failed = 0;
    $cacheDir = __DIR__ . '/assets/cached_images/';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0775, true);
    }

    foreach ($products as $p) {
        $src = $p['image_primary'];
        $cacheKey = md5($src);
        $cacheFile = $cacheDir . $cacheKey . '.jpg';

        if (file_exists($cacheFile)) {
            echo "  [CACHED] {$p['name']}\n";
            $cached++;
            continue;
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 15,
                'header' => implode("\r\n", [
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
                    "Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
                    "Accept-Language: en-US,en;q=0.9",
                    "Referer: https://www.asos.com/",
                ]),
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $imageData = @file_get_contents($src, false, $ctx);
        if ($imageData && strlen($imageData) > 100) {
            $httpCode = $http_response_header ? explode(' ', $http_response_header[0])[1] : 500;
            if ($httpCode == 200) {
                file_put_contents($cacheFile, $imageData);
                file_put_contents(
                    $cacheDir . $cacheKey . '.info',
                    json_encode(['src' => $src, 'cached_at' => time(), 'mime' => 'image/jpeg'])
                );
                echo "  [OK] {$p['name']}\n";
                $cached++;
            } else {
                echo "  [HTTP $httpCode] {$p['name']}\n";
                $failed++;
            }
        } else {
            echo "  [FAIL] {$p['name']}\n";
            $failed++;
        }
    }

    echo "\n--- Results ---\n";
    echo "Cached: $cached\n";
    echo "Failed: $failed\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
