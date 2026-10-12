<?php
// Ensure directories exist
$ktpDir = __DIR__ . '/../../storage/app/public/ktp';
$selfieDir = __DIR__ . '/../../storage/app/public/selfie';
if (!is_dir($ktpDir)) mkdir($ktpDir, 0777, true);
if (!is_dir($selfieDir)) mkdir($selfieDir, 0777, true);

// Create simple 1x1 or small image placeholder using GD
$img = imagecreatetruecolor(400, 250);
$bg = imagecolorallocate($img, 30, 64, 175);
$textColor = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $bg);
imagestring($img, 5, 120, 110, "KTP SIMULASI", $textColor);
imagepng($img, $ktpDir . '/sample_ktp.png');
imagepng($img, $selfieDir . '/sample_selfie.png');
imagedestroy($img);
echo "Dummy sample images generated.\n";
