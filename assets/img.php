<?php
$src = $_GET['src'] ?? '';
$w = (int)($_GET['w'] ?? 300);
$h = (int)($_GET['h'] ?? 400);

if (!$src) {
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'"><rect fill="#eee" width="'.$w.'" height="'.$h.'"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-family="sans-serif" font-size="14">No Image</text></svg>';
    exit;
}

$origPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $src);

if (!file_exists($origPath)) {
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'"><rect fill="#eee" width="'.$w.'" height="'.$h.'"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-family="sans-serif" font-size="14">Not Found</text></svg>';
    exit;
}

$info = @getimagesize($origPath);
if (!$info) {
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'"><rect fill="#eee" width="'.$w.'" height="'.$h.'"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-family="sans-serif" font-size="14">Invalid</text></svg>';
    exit;
}

$origW = $info[0];
$origH = $info[1];

$useGd = function_exists('imagecreatefromjpeg');

if ($useGd && ($origW < $w || $origH < $h)) {
    $ext = strtolower(pathinfo($origPath, PATHINFO_EXTENSION));
    $srcImg = null;
    switch ($ext) {
        case 'jpg': case 'jpeg': $srcImg = @imagecreatefromjpeg($origPath); break;
        case 'png': $srcImg = @imagecreatefrompng($origPath); break;
        case 'gif': $srcImg = @imagecreatefromgif($origPath); break;
        case 'webp': $srcImg = @imagecreatefromwebp($origPath); break;
    }
    if ($srcImg) {
        $dstImg = imagecreatetruecolor($w, $h);
        imagefill($dstImg, 0, 0, imagecolorallocate($dstImg, 248, 249, 250));
        $srcRatio = $origW / $origH;
        $dstRatio = $w / $h;
        if ($srcRatio > $dstRatio) {
            $drawW = $w;
            $drawH = (int)($w / $srcRatio);
        } else {
            $drawH = $h;
            $drawW = (int)($h * $srcRatio);
        }
        $dstX = (int)(($w - $drawW) / 2);
        $dstY = (int)(($h - $drawH) / 2);
        imagecopyresampled($dstImg, $srcImg, $dstX, $dstY, 0, 0, $drawW, $drawH, $origW, $origH);
        imagedestroy($srcImg);
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=86400');
        imagejpeg($dstImg, null, 85);
        imagedestroy($dstImg);
        exit;
    }
}

header('Content-Type: ' . $info['mime']);
header('Cache-Control: public, max-age=86400');
readfile($origPath);
