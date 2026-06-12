<?php
require_once __DIR__ . '/config/database.php';

echo "=== ASOS Fashion Import ===\n\n";

try {
    $pdo = getDbConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ---- 1. Clear existing product data ----
    echo "Clearing existing data...\n";
    $pdo->exec("DELETE FROM reviews");
    $pdo->exec("DELETE FROM coupon_usage");
    $pdo->exec("DELETE FROM coupons");
    $pdo->exec("DELETE FROM cart");
    $pdo->exec("DELETE FROM order_items");
    $pdo->exec("DELETE FROM orders");
    $pdo->exec("DELETE FROM wishlist");
    $pdo->exec("DELETE FROM user_activities");
    $pdo->exec("DELETE FROM product_images");
    $pdo->exec("DELETE FROM products WHERE status != 'active'");
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM categories");

    // ---- 2. Create product-type categories ----
    echo "Creating categories...\n";
    $catStmt = $pdo->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, 'active')");
    $categories = [
        "Dresses"           => ["Dresses", "dresses", "Mini, midi, maxi, formal, casual and everything in between"],
        "Coats & Jackets"   => ["Coats & Jackets", "coats-jackets", "Coats, jackets, blazers, gilets, puffers and parkas"],
        "Tops"              => ["Tops", "tops", "Everyday tops, camis, bodysuits and more"],
        "Shirts & Blouses"  => ["Shirts & Blouses", "shirts-blouses", "Button-down shirts, blouses and formal tops"],
        "Knitwear & Jumpers" => ["Knitwear & Jumpers", "knitwear-jumpers", "Jumpers, cardigans, knits and sweaters"],
        "Trousers & Jeans"  => ["Trousers & Jeans", "trousers-jeans", "Trousers, jeans, chinos and cargo pants"],
        "Bodysuits & Jumpsuits" => ["Bodysuits & Jumpsuits", "bodysuits-jumpsuits", "Bodysuits, jumpsuits and playsuits"],
        "Skirts"            => ["Skirts", "skirts", "Mini, midi, maxi skirts in every style"],
        "Shorts & Leggings" => ["Shorts & Leggings", "shorts-leggings", "Shorts, leggings and casual bottoms"],
        "Hoodies"           => ["Hoodies", "hoodies", "Hoodies and sweatshirts"],
    ];
    $catIds = [];
    foreach ($categories as $key => $cat) {
        $catStmt->execute($cat);
        $catIds[$key] = (int)$pdo->lastInsertId();
    }

    // ---- 3. Map product name to category ----
    $catRules = [
        "Dresses"           => ['dress'],
        "Coats & Jackets"   => ['coat', 'jacket', 'blazer', 'gilet', 'puffer', 'parka', 'bomber', 'waistcoat'],
        "Tops"              => ['top', 'cami', 'vest'],
        "Shirts & Blouses"  => ['shirt', 'blouse'],
        "Knitwear & Jumpers" => ['jumper', 'cardigan', 'knit', 'sweater'],
        "Trousers & Jeans"  => ['trouser', 'jean', 'cargo', 'chino'],
        "Bodysuits & Jumpsuits" => ['bodysuit', 'jumpsuit', 'playsuit'],
        "Skirts"            => ['skirt'],
        "Shorts & Leggings" => ['short', 'legging'],
        "Hoodies"           => ['hoodie'],
    ];

    function mapCategory(string $name, array $rules): string {
        $lower = strtolower($name);
        foreach ($rules as $cat => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lower, $kw)) return $cat;
            }
        }
        return "Dresses";
    }

    function parseDescription(string $raw): string {
        // Python-like list of dicts e.g. [{'Key': 'Value'}, {'Key': 'Value'}]
        // Extract meaningful text
        $text = '';
        $parts = explode("},", $raw);
        foreach ($parts as $part) {
            $part = trim($part, "[]{}' \t\n\r\0\x0B");
            $part = preg_replace("/^[^:]+:/", '', $part);
            $part = str_replace(['{', '}', '[', ']', "'", '"'], '', $part);
            $part = trim($part);
            if ($part) $text .= $part . "\n";
        }
        return trim($text) ?: 'ASOS fashion product';
    }

    function extractFirstImage(string $raw): ?string {
        // Python list of URLs e.g. ['url1', 'url2', ...]
        if (preg_match("/https?:\\/\\/[^'\"\\]]+/", $raw, $m)) {
            return $m[0];
        }
        return null;
    }

    function generateSlug(string $name): string {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $name), '-'));
        return substr($slug, 0, 100);
    }

    function parseSizes(string $raw): array {
        $raw = trim($raw, '"\'[]');
        $sizes = explode(',', $raw);
        $result = [];
        foreach ($sizes as $s) {
            $s = trim($s, '"\' ');
            // Skip stock info like "Out of stock"
            if (stripos($s, 'out of stock') !== false || stripos($s, 'stock') !== false) continue;
            if ($s) $result[] = $s;
        }
        return $result;
    }

    // ---- 4. Read CSV ----
    $csvFile = __DIR__ . '/assets/images/products_asos.csv';
    if (!file_exists($csvFile)) {
        die("CSV not found: $csvFile\n");
    }

    echo "Reading CSV...\n";
    $handle = fopen($csvFile, 'r');
    $headers = fgetcsv($handle);
    $urlCol = array_search('url', $headers);
    $nameCol = array_search('name', $headers);
    $sizeCol = array_search('size', $headers);
    $catCol = array_search('category', $headers);
    $priceCol = array_search('price', $headers);
    $colorCol = array_search('color', $headers);
    $skuCol = array_search('sku', $headers);
    $descCol = array_search('description', $headers);
    $imgCol = array_search('images', $headers);

    if (false === $nameCol || false === $priceCol || false === $skuCol) {
        die("Required columns not found in CSV\n");
    }

    // ---- 5. Import products ----
    $insertStmt = $pdo->prepare("INSERT INTO products (name, slug, short_description, description, price, compare_price, sku, stock_quantity, category_id, image_primary, is_featured, is_new, is_on_sale, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");

    $gbpToKes = 150;
    $imported = 0;
    $skipped = 0;
    $usedSkus = [];
    $batchSize = 500;

    echo "Importing products (30,845 rows)...\n";

    $pdo->beginTransaction();
    $rowCount = 0;
    $batchCount = 0;

    while (($row = fgetcsv($handle)) !== false) {
        $rowCount++;

        $name = trim($row[$nameCol] ?? '');
        $sku = trim($row[$skuCol] ?? '');
        $priceGbp = (float)($row[$priceCol] ?? 0);
        $color = trim($row[$colorCol] ?? '');
        $rawImages = $row[$imgCol] ?? '';
        $rawDesc = $row[$descCol] ?? '';

        // Skip invalid products
        if (!$name || !$sku || $priceGbp <= 0) {
            $skipped++;
            continue;
        }

        // Skip duplicate SKUs
        if (isset($usedSkus[$sku])) {
            $skipped++;
            continue;
        }
        $usedSkus[$sku] = true;

        // Convert price: GBP to KES
        $price = round($priceGbp * $gbpToKes);

        // Generate compare price (20% chance, 10-30% higher)
        $comparePrice = null;
        if (rand(1, 5) === 1) {
            $comparePrice = round($price * (1 + (rand(10, 30) / 100)));
        }

        // Category mapping
        $catName = mapCategory($name, $catRules);
        $categoryId = $catIds[$catName] ?? $catIds["Dresses"];

        // Image
        $imageUrl = extractFirstImage($rawImages);

        // Description
        $description = parseDescription($rawDesc);
        $shortDesc = mb_substr($description, 0, 150);

        // Slug
        $slug = generateSlug($name);
        $baseSlug = $slug;
        $counter = 1;
        // Ensure unique slug
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ?");
        while (true) {
            $checkStmt->execute([$slug]);
            if ($checkStmt->fetchColumn() == 0) break;
            $slug = $baseSlug . '-' . $counter++;
        }

        // Stock
        $stock = rand(20, 150);

        // Flags
        $featured = rand(1, 6) === 1 ? 1 : 0;
        $isNew = rand(1, 5) === 1 ? 1 : 0;
        $onSale = $comparePrice ? 1 : 0;

        $insertStmt->execute([
            $name, $slug, $shortDesc, $description, (float)$price,
            $comparePrice ? (float)$comparePrice : null,
            $sku, $stock, $categoryId, $imageUrl,
            $featured, $isNew, $onSale
        ]);

        $imported++;

        // Commit in batches
        if ($imported % $batchSize === 0) {
            $pdo->commit();
            echo "  Imported $imported products...\n";
            $pdo->beginTransaction();
        }

        // Safety limit - import max 15000 products
        if ($imported >= 15000) {
            echo "  Reached 15000 product limit, stopping.\n";
            break;
        }
    }

    fclose($handle);

    if ($pdo->inTransaction()) {
        $pdo->commit();
    }

    echo "\n--- Product Import Complete ---\n";
    echo "Total rows processed: $rowCount\n";
    echo "Products imported: $imported\n";
    echo "Skipped (invalid/duplicate): $skipped\n";

    // ---- 6. Add sample reviews ----
    echo "\nAdding sample reviews...\n";
    $reviewStmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved) VALUES (?, NULL, ?, ?, ?, 1)");
    $productIds = $pdo->query("SELECT id FROM products WHERE status='active' ORDER BY RAND() LIMIT 50")->fetchAll(PDO::FETCH_COLUMN);
    $reviewTexts = [
        [5, 'Love it!', 'Amazing quality and fits perfectly. Fast delivery too!'],
        [4, 'Great purchase', 'Really happy with this. Looks just like the photos.'],
        [5, 'Excellent quality', 'Very comfortable and stylish. Would buy again.'],
        [4, 'Highly recommend', 'Nice fabric and great fit. True to size.'],
        [3, 'Decent for the price', 'Good quality but colour slightly different in person.'],
        [5, 'Perfect!', 'Exactly what I was looking for. Great value.'],
        [4, 'Love this brand', 'Consistently good quality. Fast shipping.'],
        [3, 'Average', 'Okay product. Runs a bit small, size up.'],
    ];
    foreach ($productIds as $pid) {
        $rv = $reviewTexts[array_rand($reviewTexts)];
        $reviewStmt->execute([$pid, $rv[0], $rv[1], $rv[2]]);
    }

    echo "Sample reviews added: " . count($productIds) . "\n";

    // ---- 7. Update store settings ----
    echo "\nUpdating store settings...\n";
    $pdo->exec("UPDATE settings SET setting_value = 'SmartFashion' WHERE setting_key = 'store_name'");
    $pdo->exec("UPDATE settings SET setting_value = 'KES' WHERE setting_key = 'store_currency'");

    echo "\n=== Migration Complete! ===\n";
    echo "Access your store at: http://localhost/smartshopping\n";

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
