<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 21:35
 */

require(__DIR__ . '/vendor/autoload.php');
include (__DIR__ . '/imageSmoothArc.php');

$renderer = new \BaconQrCode\Renderer\Image\Png();
$renderer->setHeight(800);
$renderer->setWidth(800);
$writer = new \BaconQrCode\Writer($renderer);
$writer->writeFile('http://www.sabgames.org', 'qrcode.png');



//$img = imageCreateTrueColor( 648, 648 );
//imagealphablending($img,true);
//$color = imageColorAllocate( $img, 255, 255, 255);
//imagefill( $img, 5, 5, $color );
//
//imageSmoothArc ( $img, 648/2, 648/2, 320,640, array(0,0,0,0),M_PI+1 , M_PI+0.3);

?>
<img src="qrcode.png"/>
