<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 21:35
 */

require(__DIR__ . '/vendor/autoload.php');

$renderer = new \BaconQrCode\Renderer\Image\Png();
$renderer->setHeight(800);
$renderer->setWidth(800);
$writer = new \BaconQrCode\Writer($renderer);
$writer->writeFile('http://www.sabgames.org', 'qrcode.png');

?>
<img src="qrcode.png"/>
