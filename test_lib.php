<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 03.11.2017
 * Time: 1:39
 */

if (extension_loaded('gd')) {
    echo '<pre>';
    print_r(gd_info());
    echo '</pre>';
} else {
    echo 'GD is not available.';
}

if (extension_loaded('imagick')) {
    $imagick = new Imagick();
    echo '<pre>';
    print_r($imagick->queryFormats());
    echo '</pre>';
} else {
    echo 'ImageMagick is not available.';
}