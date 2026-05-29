<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDbConnection();

    // Update store name
    $pdo->exec("UPDATE settings SET setting_value = 'SmartFashion' WHERE setting_key = 'store_name'");
    $pdo->exec("UPDATE settings SET setting_value = 'KES' WHERE setting_key = 'store_currency'");

    // Delete old data
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

    // Create fashion categories
    $catStmt = $pdo->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, 'active')");
    $categories = [
        ["Men's Clothing", "mens-clothing", "Trendy men's fashion including shirts, t-shirts, jeans, and more"],
        ["Women's Clothing", "womens-clothing", "Stylish women's fashion including tops, dresses, bottoms, and more"],
        ["Kids' Fashion", "kids-fashion", "Cute and comfortable fashion for boys and girls"],
        ["Accessories", "accessories", "Watches, bags, belts, and other fashion accessories"],
        ["Footwear", "footwear", "Shoes, sandals, sneakers, and other footwear"],
    ];
    $categoryIds = [];
    foreach ($categories as $cat) {
        $catStmt->execute($cat);
        $categoryIds[$cat[0]] = (int)$pdo->lastInsertId();
    }

    // Read CSV
    $csvFile = __DIR__ . '/assets/images/myntradataset/styles.csv';
    if (!file_exists($csvFile)) {
        die("CSV file not found: $csvFile\n");
    }

    $rows = array_map('str_getcsv', file($csvFile));
    $header = array_shift($rows);

    // Map CSV columns to indices
    $idIdx = array_search('id', $header);
    $genderIdx = array_search('gender', $header);
    $masterCatIdx = array_search('masterCategory', $header);
    $articleTypeIdx = array_search('articleType', $header);
    $colourIdx = array_search('baseColour', $header);
    $seasonIdx = array_search('season', $header);
    $usageIdx = array_search('usage', $header);
    $nameIdx = array_search('productDisplayName', $header);

    // Category mapping: gender+masterCategory → category name
    function getCategoryKey($gender, $masterCat) {
        if ($masterCat === 'Footwear') return 'Footwear';
        if ($masterCat === 'Accessories') return 'Accessories';
        if (in_array($gender, ['Men', 'Women']) && $masterCat === 'Apparel') return $gender === 'Men' ? "Men's Clothing" : "Women's Clothing";
        if (in_array($gender, ['Boys', 'Girls'])) return "Kids' Fashion";
        return null;
    }

    // Select ~80 products spread across categories
    $selections = [
        "Men's Clothing" => ['Men', 'Apparel', 15],
        "Women's Clothing" => ['Women', 'Apparel', 15],
        "Kids' Fashion" => [['Boys','Girls'], 'Apparel', 10],
        "Accessories" => [['Men','Women'], 'Accessories', 10],
        "Footwear" => [null, 'Footwear', 10],
    ];

    $productStmt = $pdo->prepare("INSERT INTO products (name, slug, short_description, description, price, compare_price, sku, stock_quantity, category_id, image_primary, is_featured, is_new, is_on_sale, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");

    $imported = 0;
    $usedIds = [];

    foreach ($selections as $catName => $spec) {
        $targetCount = $spec[2];
        $count = 0;

        foreach ($rows as $row) {
            if ($count >= $targetCount) break;

            $id = $row[$idIdx];
            $gender = $row[$genderIdx];
            $masterCat = $row[$masterCatIdx];
            $articleType = $row[$articleTypeIdx];
            $colour = $row[$colourIdx];
            $season = $row[$seasonIdx];
            $usage = $row[$usageIdx];
            $name = $row[$nameIdx];

            $key = getCategoryKey($gender, $masterCat);
            if ($key !== $catName) continue;
            if (in_array($id, $usedIds)) continue;

            $usedIds[] = $id;
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $name), '-'));
            $slug = substr($slug, 0, 80);

            // Price logic
            $price = 0;
            if ($masterCat === 'Accessories') {
                $price = rand(8, 50) * 100; // 800-5000
            } elseif ($masterCat === 'Footwear') {
                $price = rand(15, 120) * 100; // 1500-12000
            } elseif ($articleType === 'Watches') {
                $price = rand(20, 150) * 100; // 2000-15000
            } else {
                $price = rand(8, 80) * 100; // 800-8000
            }

            $comparePrice = rand(0, 3) === 0 ? $price + rand(5, 40) * 100 : null;
            $stock = rand(15, 100);
            $desc = "A stylish $colour $articleType for $gender. Perfect for $usage wear, $season collection.";
            $sku = 'SF-' . $articleType . '-' . $id;
            $image = 'assets/images/myntradataset/images/' . $id . '.jpg';
            $featured = rand(1, 5) === 1 ? 1 : 0;
            $isNew = rand(1, 4) === 1 ? 1 : 0;
            $onSale = $comparePrice ? 1 : 0;

            $productStmt->execute([$name, $slug, $desc, $desc, (float)$price, $comparePrice ? (float)$comparePrice : null, $sku, $stock, $categoryIds[$catName], $image, $featured, $isNew, $onSale]);
            $count++;
            $imported++;
        }
    }

    // Add some sample reviews
    $reviewStmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved) VALUES (?, NULL, ?, ?, ?, 1)");
    $productIds = $pdo->query("SELECT id FROM products WHERE status='active' ORDER BY RAND() LIMIT 20")->fetchAll(PDO::FETCH_COLUMN);
    $reviewTexts = [
        [5, 'Love it!', 'Great quality and fits perfectly.'],
        [4, 'Good purchase', 'Nice product for the price.'],
        [5, 'Excellent!', 'Very comfortable and stylish.'],
        [3, 'Decent', 'Okay quality but runs small.'],
        [4, 'Happy with this', 'Looks great, fast shipping.'],
    ];
    foreach ($productIds as $pid) {
        $rv = $reviewTexts[array_rand($reviewTexts)];
        $reviewStmt->execute([$pid, $rv[0], $rv[1], $rv[2]]);
    }

    echo "Migration complete!\n";
    echo "Categories created: " . count($categories) . "\n";
    echo "Products imported: $imported\n";
    echo "Sample reviews added: " . count($productIds) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
