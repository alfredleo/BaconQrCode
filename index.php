<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 21:35
 */

require(__DIR__ . '/vendor/autoload.php');
include_once(__DIR__ . '/imageSmoothArc.php');
include_once('C:\wamp64\helpers\dumpphp\dumping.php');

$start = microtime(true);
$renderer = new \BaconQrCode\Renderer\Image\Png();
$renderer->setHeight(800);
$renderer->setWidth(800);
$writer = new \BaconQrCode\Writer($renderer);
$writer->writeFile('http://www.sabgames.org', 'qrcode.png');

// measure speed
$end = microtime(true);
$duration = number_format(($end - $start) * 1000, 3) . "ms";

?>
    <img src="qrcode.png"/>
<?php echo $duration; ?>