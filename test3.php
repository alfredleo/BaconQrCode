<?php
// bezier circle constant
const BCC = 0.552284749831;
bezier();
function bezier(
    $strokeColor = 'rgb(88,88,88)',
    $fillColor = 'rgba(88,99,199,0)',
    $backgroundColor = 'white'
) {
    $draw = new \ImagickDraw();

    $strokeColor = new \ImagickPixel($strokeColor);
    $fillColor = new \ImagickPixel($fillColor);

    $draw->setStrokeOpacity(1);
    $draw->setStrokeColor($strokeColor);
    $draw->setFillColor($fillColor);

    // radius
    $r = 150.0;
    $strokeW = 90.0;
    $offset = 200.0;
    // used to remove artifacts on high stroke width
    $ra = 0.01; // default is 0
    // customized stroke, stroke radius
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
//        [ // fourth small quarter circle
//            ['x' => -$r, 'y' => 0],
//            ['x' => -$r * BCC, 'y' => $r],
//            ['x' => -$r, 'y' => $r * BCC],
//            ['x' => -$r, 'y' => 0],
//        ],

    ];
    // two straight lines
    $draw->line(-$r + $offset, $offset, -$r + $offset, -$r + $sr + $offset);
    $draw->line($offset, -$r + $offset, -$r + $sr + $offset, -$r + $offset);
    foreach ($smoothPointsSet as $points) {
        foreach ($points as &$point) {
            $point['x'] += $offset;
            $point['y'] += $offset;
            $draw->point($point['x'], $point['y']);
        }
        $draw->bezier($points);
    }

    $disjointPoints = [
        [
            ['x' => 10 * 5, 'y' => 10 * 5],
            ['x' => 30 * 5, 'y' => 90 * 5],
            ['x' => 25 * 5, 'y' => 10 * 5],
            ['x' => 50 * 5, 'y' => 50 * 5],
        ],
        [
            ['x' => 50 * 5, 'y' => 50 * 5],
            ['x' => 80 * 5, 'y' => 50 * 5],
            ['x' => 70 * 5, 'y' => 10 * 5],
            ['x' => 90 * 5, 'y' => 40 * 5],
        ]
    ];
    $draw->translate(0, 200);

    foreach ($disjointPoints as $points) {
//        $draw->bezier($points);
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