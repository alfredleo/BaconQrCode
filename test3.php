<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 03.11.2017
 * Time: 1:18
 */

$start = microtime(true);
// default size
$width = 150;
$height = 100;
// ... or user defined
if (is_numeric($_REQUEST["width"])) $width = $_REQUEST["width"];
if (is_numeric($_REQUEST["height"])) $height = $_REQUEST["height"];
// create new image
$img = imagecreatetruecolor($width, $height);
// transparent background
$background = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
imagefilledrectangle($img, 0, 0, $width, $height, $background);
imagecolortransparent($img, $background);
// helper function, draws pixel and mirrors it
function setpixel4($img, $centerX, $centerY, $deltaX, $deltaY, $color)
{
    imagesetpixel($img, $centerX + $deltaX, $centerY + $deltaY, $color);
    imagesetpixel($img, $centerX - $deltaX, $centerY + $deltaY, $color);
    imagesetpixel($img, $centerX + $deltaX, $centerY - $deltaY, $color);
    imagesetpixel($img, $centerX - $deltaX, $centerY - $deltaY, $color);
}

// red ellipse, 2*10px border
$color = imagecolorallocate($img, 0xFF, 0x00, 0x00);
$centerX = $width / 2;
$radiusX = ($width - 20) / 2;
$centerY = $height / 2;
$radiusY = ($height - 20) / 2;
static $maxTransparency = 0x7F; // 127
$radiusX2 = $radiusX * $radiusX;
$radiusY2 = $radiusY * $radiusY;
// upper and lower halves
$quarter = round($radiusX2 / sqrt($radiusX2 + $radiusY2));
for ($x = 0; $x <= $quarter; $x++) {
    $y = $radiusY * sqrt(1 - $x * $x / $radiusX2);
    $error = $y - floor($y);
    $transparency = round($error * $maxTransparency);
    $alpha = $color | ($transparency << 24);
    $alpha2 = $color | (($maxTransparency - $transparency) << 24);
    setpixel4($img, $centerX, $centerY, $x, floor($y), $alpha);
    setpixel4($img, $centerX, $centerY, $x, floor($y) + 1, $alpha2);
}
// right and left halves
$quarter = round($radiusY2 / sqrt($radiusX2 + $radiusY2));
for ($y = 0; $y <= $quarter; $y++) {
    $x = $radiusX * sqrt(1 - $y * $y / $radiusY2);
    $error = $x - floor($x);
    $transparency = round($error * $maxTransparency);
    $alpha = $color | ($transparency << 24);
    $alpha2 = $color | (($maxTransparency - $transparency) << 24);
    setpixel4($img, $centerX, $centerY, floor($x), $y, $alpha);
    setpixel4($img, $centerX, $centerY, floor($x) + 1, $y, $alpha2);
}
// measure speed
$end = microtime(true);
$duration = number_format(($end - $start) * 1000, 3) . "ms";
imagestring($img, 1, $width - 50, $height - 10, $duration, 0);
// send PNG to browser
header("Content-type: image/png");
imagepng($img, NULL, 9, PNG_ALL_FILTERS);