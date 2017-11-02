<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 22:30
 */

// Setup an anti-aliased image and a normal image
$aa = imagecreatetruecolor(400, 100);
$normal = imagecreatetruecolor(200, 100);

// Switch antialiasing on for one image
imageantialias($aa, true);

// Allocate colors
$red = imagecolorallocate($normal, 255, 0, 0);
$red_aa = imagecolorallocate($aa, 255, 0, 0);

// Draw two lines, one with AA enabled
imageline($normal, 0, 0, 200, 100, $red);
imageline($aa, 0, 0, 200, 100, $red_aa);

// Merge the two images side by side for output (AA: left, Normal: Right)
imagecopymerge($aa, $normal, 200, 0, 0, 0, 200, 100, 100);

// Output image
header('Content-type: image/png');

imagepng($aa);
imagedestroy($aa);
imagedestroy($normal);