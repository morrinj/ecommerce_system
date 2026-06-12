<?php
$csv = array_map('str_getcsv', file('assets/images/products_asos.csv'));
$headers = array_shift($csv);
$nameCol = array_search('name', $headers);

$types = [];
$keywords = [
    'dress', 'coat', 'jacket', 'top', 'shirt', 'trouser', 'jumper', 'skirt',
    'jean', 'blouse', 'gilet', 'knit', 'cardigan', 'hoodie', 'sweater',
    'jumpsuit', 'playsuit', 'bodysuit', 'legging', 'short', 'blazer',
    'waistcoat', 'puffer', 'bomber', 'parka', 'cargo', 'chino',
];

foreach ($csv as $row) {
    $name = strtolower($row[$nameCol] ?? '');
    foreach ($keywords as $kw) {
        if (str_contains($name, $kw)) {
            // Normalize: coat → coat, dress → dress, mini dress → dress too
            if (str_contains($name, 'mini dress') || str_contains($name, 'midi dress') || str_contains($name, 'maxi dress')) {
                $types['dress'] = ($types['dress'] ?? 0) + 1;
            } elseif (str_contains($name, $kw)) {
                $types[$kw] = ($types[$kw] ?? 0) + 1;
            }
            break;
        }
    }
}

arsort($types);
echo "Product type distribution (first 30,000 rows):\n";
$total = 0;
foreach ($types as $type => $count) {
    echo "  $type: $count\n";
    $total += $count;
}
echo "\nTotal classified: $total\n";
echo "Total rows: " . count($csv) . "\n";
echo "Unclassified: " . (count($csv) - $total) . "\n";
