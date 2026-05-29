<?php
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=86400');

$width = (int)($_GET['w'] ?? 300);
$height = (int)($_GET['h'] ?? 300);
$text = $_GET['text'] ?? 'No Image';
$bg = $_GET['bg'] ?? 'e9ecef';
$color = $_GET['color'] ?? '6c757d';

if ($width < 1) $width = 300;
if ($height < 1) $height = 300;
if ($width > 1200) $width = 1200;
if ($height > 1200) $height = 1200;

$displayText = htmlspecialchars($text, ENT_QUOTES);
$fontSize = min($width, $height) * 0.08;
if ($fontSize < 12) $fontSize = 12;
if ($fontSize > 48) $fontSize = 48;

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" width="<?= $width ?>" height="<?= $height ?>" viewBox="0 0 <?= $width ?> <?= $height ?>">
  <rect width="100%" height="100%" fill="#<?= $bg ?>"/>
  <g transform="translate(<?= $width/2 ?>, <?= $height/2 ?>)">
    <circle cx="0" cy="-20" r="<?= min($width,$height)*0.12 ?>" fill="#<?= $color ?>" opacity="0.3"/>
    <rect x="-<?= min($width,$height)*0.15 ?>" y="<?= min($width,$height)*0.08 ?>" width="<?= min($width,$height)*0.3 ?>" height="<?= min($width,$height)*0.15 ?>" rx="4" fill="#<?= $color ?>" opacity="0.3"/>
    <text x="0" y="<?= min($width,$height)*0.28 ?>" text-anchor="middle" fill="#<?= $color ?>" font-family="Arial, sans-serif" font-size="<?= $fontSize ?>" font-weight="bold"><?= $displayText ?></text>
  </g>
</svg>
