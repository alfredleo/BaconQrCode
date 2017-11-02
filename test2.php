<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 22:32
 */


function imageSmoothCircle(&$img, $cx, $cy, $cr, $color)
{
    $ir = $cr;
    $ix = 0;
    $iy = $ir;
    $ig = 2 * $ir - 3;
    $idgr = -6;
    $idgd = 4 * $ir - 10;
    $fill = imageColorExactAlpha($img, $color['R'], $color['G'], $color['B'], 0);
    imageLine($img, $cx + $cr - 1, $cy, $cx, $cy, $fill);
    imageLine($img, $cx - $cr + 1, $cy, $cx - 1, $cy, $fill);
    imageLine($img, $cx, $cy + $cr - 1, $cx, $cy + 1, $fill);
    imageLine($img, $cx, $cy - $cr + 1, $cx, $cy - 1, $fill);
    $draw = imageColorExactAlpha($img, $color['R'], $color['G'], $color['B'], 42);
    imageSetPixel($img, $cx + $cr, $cy, $draw);
    imageSetPixel($img, $cx - $cr, $cy, $draw);
    imageSetPixel($img, $cx, $cy + $cr, $draw);
    imageSetPixel($img, $cx, $cy - $cr, $draw);
    while ($ix <= $iy - 2) {
        if ($ig < 0) {
            $ig += $idgd;
            $idgd -= 8;
            $iy--;
        } else {
            $ig += $idgr;
            $idgd -= 4;
        }
        $idgr -= 4;
        $ix++;
        imageLine($img, $cx + $ix, $cy + $iy - 1, $cx + $ix, $cy + $ix, $fill);
        imageLine($img, $cx + $ix, $cy - $iy + 1, $cx + $ix, $cy - $ix, $fill);
        imageLine($img, $cx - $ix, $cy + $iy - 1, $cx - $ix, $cy + $ix, $fill);
        imageLine($img, $cx - $ix, $cy - $iy + 1, $cx - $ix, $cy - $ix, $fill);
        imageLine($img, $cx + $iy - 1, $cy + $ix, $cx + $ix, $cy + $ix, $fill);
        imageLine($img, $cx + $iy - 1, $cy - $ix, $cx + $ix, $cy - $ix, $fill);
        imageLine($img, $cx - $iy + 1, $cy + $ix, $cx - $ix, $cy + $ix, $fill);
        imageLine($img, $cx - $iy + 1, $cy - $ix, $cx - $ix, $cy - $ix, $fill);
        $filled = 0;
        for ($xx = $ix - 0.45; $xx < $ix + 0.5; $xx += 0.2) {
            for ($yy = $iy - 0.45; $yy < $iy + 0.5; $yy += 0.2) {
                if (sqrt(pow($xx, 2) + pow($yy, 2)) < $cr) $filled += 4;
            }
        }
        $draw = imageColorExactAlpha($img, $color['R'], $color['G'], $color['B'], (100 - $filled));
        imageSetPixel($img, $cx + $ix, $cy + $iy, $draw);
        imageSetPixel($img, $cx + $ix, $cy - $iy, $draw);
        imageSetPixel($img, $cx - $ix, $cy + $iy, $draw);
        imageSetPixel($img, $cx - $ix, $cy - $iy, $draw);
        imageSetPixel($img, $cx + $iy, $cy + $ix, $draw);
        imageSetPixel($img, $cx + $iy, $cy - $ix, $draw);
        imageSetPixel($img, $cx - $iy, $cy + $ix, $draw);
        imageSetPixel($img, $cx - $iy, $cy - $ix, $draw);
    }
}

$img = imageCreateTrueColor(320, 240);

imageSmoothCircle($img, 160, 120, 100, array('R' => 0xCC, 'G' => 0x33, 'B' => 0x00));
imageSmoothCircle($img, 170, 110, 75, array('R' => 0xDD, 'G' => 0x66, 'B' => 0x00));
imageSmoothCircle($img, 180, 100, 50, array('R' => 0xEE, 'G' => 0x99, 'B' => 0x00));
imageSmoothCircle($img, 190, 90, 25, array('R' => 0xFF, 'G' => 0xCC, 'B' => 0x00));

header('Content-Type: image/png');
imagePNG($img);
