<?php
require_once __DIR__ . '/config/database.php';

echo "=== Restore ASOS Image URLs from CSV ===\n\n";

$csvFile = __DIR__ . '/assets/images/products_asos.csv';
if (!file_exists($csvFile)) {
    die("CSV not found: $csvFile\n");
}

try {
    $pdo = getDbConnection();

    // Read CSV and map SKU -> image URL
    echo "Reading CSV...\n";
    $handle = fopen($csvFile, 'r');
    $headers = fgetcsv($handle);
    $skuCol = array_search('sku', $headers);
    $imgCol = array_search('images', $headers);

    if (false === $skuCol || false === $imgCol) {
        die("Required columns not found in CSV\n");
    }

    function extractFirstImage(string $raw): ?string {
        if (preg_match("/https?:\\/\\/[^'\"\\]]+/", $raw, $m)) {
            return $m[0];
        }
        return null;
    }

    $updated = 0;
    $stmt = $pdo->prepare("UPDATE products SET image_primary = ? WHERE sku = ?");

    while (($row = fgetcsv($handle)) !== false) {
        $sku = trim($row[$skuCol] ?? '');
        $rawImages = $row[$imgCol] ?? '';

        if (!$sku || !$rawImages) continue;

        $imageUrl = extractFirstImage($rawImages);
        if (!$imageUrl) continue;

        $stmt->execute([$imageUrl, $sku]);
        if ($stmt->rowCount() > 0) {
            $updated++;
        }
    }

    fclose($handle);

    echo "Products updated with ASOS image URLs: $updated\n";

    // Verify
    $check = $pdo->query("SELECT COUNT(*) FROM products WHERE image_primary IS NOT NULL")->fetchColumn();
    $nulls = $pdo->query("SELECT COUNT(*) FROM products WHERE image_primary IS NULL")->fetchColumn();
    echo "Products with images: $check\n";
    echo "Products without images: $nulls\n";

    echo "\n=== Done! ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
