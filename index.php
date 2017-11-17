<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 21:35
 */

// for algorithms on pure gd with good antialias see http://create.stephan-brumme.com/antialiased-circle/

use BaconQrCode\Renderer\Color\Rgb;

require(__DIR__ . '/autoload_register.php');
include_once(__DIR__ . '/imageSmoothArc.php');
//include_once('C:\wamp64\helpers\dumpphp\dumping.php');

$start = microtime(true);
$renderer = new \BaconQrCode\Renderer\Image\Png();
$renderer->setHeight(500);
$renderer->setWidth(500);
$renderer->setMargin(1);
$renderer->setBackgroundColor(new Rgb(255, 255, 255));
$renderer->setForegroundColor(new Rgb(51, 49, 60));
$renderer->setFinderColor(new Rgb(245, 166, 35));
$writer = new \BaconQrCode\Writer($renderer);
$text = 'http://aaaaaaaa.bbbbbb.org/api/promotion/getPromotionInfo?id=36';
if (isset($_REQUEST["text"]) && (strlen($_REQUEST["text"]) > 0)) {
    $text = $_REQUEST["text"];
}


$writer->writeFile($text, 'qrcode.png');

// measure speed
$end = microtime(true);
$duration = number_format(($end - $start) * 1000, 3) . "ms";

?>
    <p>Use: /?text=Here goes your text</p>
    <img src="qrcode.png"/>
<?php echo $duration; ?>