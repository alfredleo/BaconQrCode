<?php
/** bezier quarter circle constant*/
const BCC = 0.552284749831;
bezier();
function bezier(
    $strokeColor = 'rgba(51,49,60,1)',
    $fillColor = 'rgba(88,99,199, 0)',
    $backgroundColor = 'white'
) {
    $draw = new \ImagickDraw();

    $strokeColor = new \ImagickPixel($strokeColor);
    $fillColor = new \ImagickPixel($fillColor);

    $draw->setStrokeColor($strokeColor);
    $draw->setFillColor($fillColor);

    /** radius of small circles in qr code. */
    $pointRadius = 10.0;

    /** 3 quarters circle radius */
    $r = 6 * $pointRadius;
    /** stroke width */
    $strokeW = $pointRadius * 2;
    /** offset from the center of coordinates */
    $offset = 80.0;
    /** used to remove artifacts on high stroke width, on 0 shows artifacts */
    $ra = 0.01;
    /** remove artifacts for small quarter circle, on 1 shows artifacts */
    $ras = 1.1;
    $draw->setStrokeWidth($strokeW);


    $smoothPointsSet = [
        [ // first quarter circle
            ['x' => 0, 'y' => -$r],
            ['x' => $r * BCC, 'y' => -$r],
            ['x' => $r, 'y' => -$r * BCC],
            ['x' => $r, 'y' => 0],
        ],
        [ // second quarter circle
            ['x' => $r, 'y' => -$r * $ra],
            ['x' => $r, 'y' => $r * BCC],
            ['x' => $r * BCC, 'y' => $r],
            ['x' => -$r * $ra, 'y' => $r],
        ],
        [ // third quarter circle
            ['x' => 0, 'y' => $r],
            ['x' => -$r * BCC, 'y' => $r],
            ['x' => -$r, 'y' => $r * BCC],
            ['x' => -$r, 'y' => 0],
        ],
        [ // fourth small quarter circle
            ['x' => -$r, 'y' => -$r + $pointRadius * $ras],
            ['x' => -$r, 'y' => -$r + $pointRadius * (1 - BCC)],
            ['x' => -$r + $pointRadius * (1 - BCC), 'y' => -$r],
            ['x' => -$r + $pointRadius * $ras, 'y' => -$r],
        ],
    ];
    // two straight lines
    $draw->line(-$r + $offset, $offset, -$r + $offset, -$r + $pointRadius + $offset);
    $draw->line($offset, -$r + $offset, -$r + $pointRadius + $offset, -$r + $offset);

    foreach ($smoothPointsSet as $points) {
        foreach ($points as &$point) {
            $point['x'] += $offset;
            $point['y'] += $offset;
//            $draw->point($point['x'], $point['y']);
        }
        $draw->bezier($points);
    }

    // draw middle circle
    $draw->setStrokeOpacity(0);
    $draw->setFillColor('rgb(245,166,35)');
    $draw->circle($offset, $offset, $offset, $offset + 3 * $pointRadius);

    // Create an image object which the draw commands can be rendered into
    $imagick = new \Imagick();
    $imagick->newImage(500, 500, $backgroundColor);
    $imagick->setImageFormat("png");

    // Render the draw commands in the ImagickDraw object into the image.
    $imagick->drawImage($draw);

    // Send the image to the browser
    header("Content-Type: image/png");
    echo $imagick->getImageBlob();
}