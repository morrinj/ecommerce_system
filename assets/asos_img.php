<?php
$src = $_GET['src'] ?? '';
if (!$src) {
    header('Content-Type: image/svg+xml');
    readfile(__DIR__ . '/placeholder.php?w=300&h=300&text=No+Image');
    exit;
}

$cacheDir = __DIR__ . '/cached_images/';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0775, true);
}

$cacheKey = md5($src);
$cacheFile = $cacheDir . $cacheKey . '.jpg';
$cacheInfo = $cacheDir . $cacheKey . '.info';

$maxAge = 86400 * 30;

// Serve from cache if available and fresh
if (file_exists($cacheFile) && file_exists($cacheInfo)) {
    $info = json_decode(file_get_contents($cacheInfo), true);
    if ($info && (time() - $info['cached_at']) < $maxAge) {
        $mime = $info['mime'] ?? 'image/jpeg';
        header("Content-Type: $mime");
        header('Cache-Control: public, max-age=86400');
        readfile($cacheFile);
        exit;
    }
}

// Try to download
$ctx = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 30,
        'header' => implode("\r\n", [
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
            "Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.9",
            "Referer: https://www.asos.com/",
            "Sec-Fetch-Dest: image",
            "Sec-Fetch-Mode: no-cors",
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
        $mime = 'image/jpeg';
        foreach ($http_response_header as $h) {
            if (stripos($h, 'Content-Type:') === 0) {
                $mime = trim(substr($h, 13));
                break;
            }
        }

        file_put_contents($cacheFile, $imageData);
        file_put_contents($cacheInfo, json_encode([
            'src' => $src,
            'mime' => $mime,
            'cached_at' => time(),
        ]));

        header("Content-Type: $mime");
        header('Cache-Control: public, max-age=86400');
        echo $imageData;
        exit;
    }
}

// Download failed -> generate placeholder
$text = $_GET['text'] ?? 'Product Image';
$w = (int)($_GET['w'] ?? 300);
$h = (int)($_GET['h'] ?? 400);

header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=3600');

$displayText = htmlspecialchars($text, ENT_QUOTES);
$fontSize = min($w, $h) * 0.07;
if ($fontSize < 12) $fontSize = 12;
if ($fontSize > 40) $fontSize = 40;
$bg = 'e9ecef';
$color = '6c757d';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" width="<?= $w ?>" height="<?= $h ?>" viewBox="0 0 <?= $w ?> <?= $h ?>">
  <rect width="100%" height="100%" fill="#<?= $bg ?>"/>
  <g transform="translate(<?= $w/2 ?>, <?= $h/2 ?>)">
    <circle cx="0" cy="-20" r="<?= min($w,$h)*0.12 ?>" fill="#<?= $color ?>" opacity="0.3"/>
    <rect x="-<?= min($w,$h)*0.15 ?>" y="<?= min($w,$h)*0.08 ?>" width="<?= min($w,$h)*0.3 ?>" height="<?= min($w,$h)*0.15 ?>" rx="4" fill="#<?= $color ?>" opacity="0.3"/>
    <text x="0" y="<?= min($w,$h)*0.28 ?>" text-anchor="middle" fill="#<?= $color ?>" font-family="Arial, sans-serif" font-size="<?= $fontSize ?>"><?= $displayText ?></text>
  </g>
</svg>
