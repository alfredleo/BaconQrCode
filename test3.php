<?php
/** bezier quarter circle constant*/
const BCC = 0.552284749831;
bezier();
function bezier(
    $strokeColor = 'rgba(88,88,88, 1)',
    $fillColor = 'rgba(88,99,199,0)',
    $backgroundColor = 'white'
) {
    $draw = new \ImagickDraw();

    $strokeColor = new \ImagickPixel($strokeColor);
    $fillColor = new \ImagickPixel($fillColor);

    $draw->setStrokeColor($strokeColor);
    $draw->setFillColor($fillColor);

    /** 3 quarters circle radius */
    $r = 40.0;
    /** stroke width */
    $strokeW = 10.0;
    /** offset from the center of coordinates */
    $offset = 50.0;
    /** used to remove artifacts on high stroke width, on 0 shows artifacts */
    $ra = 0.01;
    /** remove artifacts for small quarter circle, on 1 shows artifacts */
    $ras = 1.1;
    /** stroke radius*/
    $sr = $strokeW / 2.0;
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
            ['x' => -$r, 'y' => -$r + $sr * $ras],
            ['x' => -$r, 'y' => -$r + $sr * (1 - BCC)],
            ['x' => -$r + $sr * (1 - BCC), 'y' => -$r],
            ['x' => -$r + $sr * $ras, 'y' => -$r],
        ],
    ];
    // two straight lines
    $draw->line(-$r + $offset, $offset, -$r + $offset, -$r + $sr + $offset);
    $draw->line($offset, -$r + $offset, -$r + $sr + $offset, -$r + $offset);

    foreach ($smoothPointsSet as $points) {
        foreach ($points as &$point) {
            $point['x'] += $offset;
            $point['y'] += $offset;
//            $draw->point($point['x'], $point['y']);
        }
        $draw->bezier($points);
    }

//Create an image object which the draw commands can be rendered into
    $imagick = new \Imagick();
    $imagick->newImage(500, 500, $backgroundColor);
    $imagick->setImageFormat("png");

//Render the draw commands in the ImagickDraw object
//into the image.
    $imagick->drawImage($draw);

//Send the image to the browser
    header("Content-Type: image/png");
    echo $imagick->getImageBlob();
}