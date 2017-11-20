<?php

$strokeColor = 'rgba(51,49,60,1)';
$fillColor = 'rgba(88,99,199, 0)';
$backgroundColor = 'white';
$draw = new \ImagickDraw();
$strokeColor = new \ImagickPixel($strokeColor);
$fillColor = new \ImagickPixel($fillColor);
$draw->setStrokeColor($strokeColor);
$draw->setFillColor($fillColor);
/** float radius of small circles in qr code */
$pointRadius = 10.0;
/** size of qr code points */
$pointCount = 25;
$offsetFromBorder = 5;
$offset = 7 * $pointRadius + $offsetFromBorder;
$offsetOuter = $pointCount * $pointRadius * 2 - 7 * $pointRadius;

bezier($offset, $offset, 0, $pointRadius, $draw);
bezier($offsetOuter, $offset, 90, $pointRadius, $draw);
bezier($offset, $offsetOuter, -90, $pointRadius, $draw);

// Create an image object which the draw commands can be rendered into
$imagick = new \Imagick();
$imagick->newImage(510, 510, $backgroundColor);
$imagick->setImageFormat("png");

// Render the draw commands in the ImagickDraw object into the image.
$imagick->drawImage($draw);

// Send the image to the browser
header("Content-Type: image/png");
echo $imagick->getImageBlob();

/**
 * Draws custom figure
 * @param $offsetX
 * @param $offsetY
 * @param $rotate
 * @param $pointRadius float radius of small circles in qr code
 * @param $draw \ImagickDraw
 */
function bezier(
    $offsetX,
    $offsetY,
    $rotate,
    $pointRadius,
    $draw
) {
    /** 3 quarters circle radius */
    $r = 6 * $pointRadius;
    /** stroke width */
    $strokeW = $pointRadius * 2;
    /** used to remove artifacts on high stroke width, on 0 shows artifacts */
    $ra = 0.01;
    /** remove artifacts for small quarter circle, on 1 shows artifacts */
    $ras = 1.1;
    /** bezier quarter circle constant*/
    $BCC = 0.552284749831;

    $draw->translate($offsetX, $offsetY);
    $draw->rotate($rotate);
    $draw->setStrokeWidth($strokeW);
    // draw middle circle

    $draw->setStrokeOpacity(0);
    $fillColor = $draw->getFillColor();
    $draw->setFillColor('rgb(245,166,35)');
    $draw->circle(0, 0, 0, 3 * $pointRadius);
    $draw->setStrokeOpacity(1);
    $draw->setFillColor($fillColor);

    $smoothPointsSet = [
        [ // first quarter circle
            ['x' => 0, 'y' => -$r],
            ['x' => $r * $BCC, 'y' => -$r],
            ['x' => $r, 'y' => -$r * $BCC],
            ['x' => $r, 'y' => 0],
        ],
        [ // second quarter circle
            ['x' => $r, 'y' => -$r * $ra],
            ['x' => $r, 'y' => $r * $BCC],
            ['x' => $r * $BCC, 'y' => $r],
            ['x' => -$r * $ra, 'y' => $r],
        ],
        [ // third quarter circle
            ['x' => 0, 'y' => $r],
            ['x' => -$r * $BCC, 'y' => $r],
            ['x' => -$r, 'y' => $r * $BCC],
            ['x' => -$r, 'y' => 0],
        ],
        [ // fourth small quarter circle
            ['x' => -$r, 'y' => -$r + $pointRadius * $ras],
            ['x' => -$r, 'y' => -$r + $pointRadius * (1 - $BCC)],
            ['x' => -$r + $pointRadius * (1 - $BCC), 'y' => -$r],
            ['x' => -$r + $pointRadius * $ras, 'y' => -$r],
        ],
    ];
    // two straight lines
    $draw->line(-$r, 0, -$r, -$r + $pointRadius);
    $draw->line(0, -$r, -$r + $pointRadius, -$r);

    foreach ($smoothPointsSet as $points) {
        $draw->bezier($points);
    }

    $draw->rotate(-$rotate);
    $draw->translate(-$offsetX, -$offsetY);
}