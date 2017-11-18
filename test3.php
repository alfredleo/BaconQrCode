<?php
// bezier circle constant
const BCC = 0.552284749831;
bezier();
function bezier(
    $strokeColor = 'rgb(88,88,88)',
    $fillColor = 'rgba(88,99,199,1)',
    $backgroundColor = 'white'
) {
    $draw = new \ImagickDraw();

    $strokeColor = new \ImagickPixel($strokeColor);
    $fillColor = new \ImagickPixel($fillColor);

    $draw->setStrokeOpacity(1);
    $draw->setStrokeColor($strokeColor);
    $draw->setFillColor($fillColor);

    $draw->setStrokeWidth(20);
    // radius
    $r = 100;
    $offset = 150;


    $smoothPointsSet = [
        [
            ['x' => 0, 'y' => -$r],
            ['x' => $r * BCC, 'y' => -$r],
            ['x' => $r, 'y' => -$r * BCC],
            ['x' => $r, 'y' => 0],
        ],
        [
            ['x' => $r, 'y' => 0],
            ['x' => $r, 'y' => $r * BCC],
            ['x' => $r * BCC, 'y' => $r],
            ['x' => 0, 'y' => $r],
        ],
        [
            ['x' => 0, 'y' => $r],
            ['x' => -$r * BCC, 'y' => $r],
            ['x' => -$r, 'y' => $r * BCC],
            ['x' => -$r, 'y' => 0],
        ],
        [
            ['x' => -$r, 'y' => 0],
            ['x' => -$r, 'y' => -$r * 0.8],
            ['x' => -$r, 'y' => 0],
            ['x' => -$r, 'y' => -$r * 0.8],
        ],

    ];
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