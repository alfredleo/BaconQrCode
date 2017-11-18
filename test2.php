<?php

const BEZIER_CIRCLE_CONSTANT = 0.552284749831;

function pathCurveToQuadraticBezierAbsolute(
    $strokeColor = 'rgb(1,1,1)',
    $fillColor = 'rgba(0,0,0,0)',
    $backgroundColor = 'white'
) {

    $draw = new \ImagickDraw();

    $draw->setStrokeOpacity(1);
    $draw->setStrokeColor($strokeColor);
    $draw->setFillColor($fillColor);

    $draw->setStrokeWidth(2);
    $draw->setFontSize(72);

//    $draw->circle(0,0,5,5);
    $draw->pathStart();
    $draw->pathMoveToAbsolute(50, 250);


    // This specifies a quadratic bezier curve with the current position as the start
    // point, the control point is the first two params, and the end point is the last two params.
    $draw->pathCurveToQuadraticBezierAbsolute(
        150, 50,
        250, 250
    );

    // This specifies a quadratic bezier curve with the current position as the start
    // point, the control point is mirrored from the previous curves control point
    // and the end point is defined by the x, y values.
    $draw->pathCurveToQuadraticBezierSmoothAbsolute(
        450, 250
    );
/*
    // This specifies a quadratic bezier curve with the current position as the start
    // point, the control point is mirrored from the previous curves control point
    // and the end point is defined relative from the current position by the x, y values.
    $draw->pathCurveToQuadraticBezierSmoothRelative(
        200, -100
    );*/

    $draw->pathFinish();

    $imagick = new \Imagick();
    $imagick->newImage(700, 500, $backgroundColor);
    $imagick->setImageFormat("png");

    $imagick->drawImage($draw);

    header("Content-Type: image/png");
    echo $imagick->getImageBlob();

}

pathCurveToQuadraticBezierAbsolute();