<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 21:35
 */

// for algorithms on pure gd with good antialias see http://create.stephan-brumme.com/antialiased-circle/

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Color\Rgb;

require(__DIR__ . '/autoload_register.php');
include_once(__DIR__ . '/imageSmoothArc.php');
//include_once('C:\wamp64\helpers\dumpphp\dumping.php');

$start = microtime(true);
$renderer = new \BaconQrCode\Renderer\Image\PngImagick();
$renderer->setHeight(900);
$renderer->setWidth(900);
$renderer->setMargin(0);
$renderer->setBackgroundColor(new Rgb(255, 255, 255));
$renderer->setForegroundColor(new Rgb(34, 37, 42));
$renderer->setFinderColor(new Rgb(208, 2, 27));
$writer = new \BaconQrCode\Writer($renderer);
$text = 'http://aaaaaaaa.bbbbbb.org/api/promotion/getPromotionInfo?id=36';
if (isset($_REQUEST["text"]) && (strlen($_REQUEST["text"]) > 0)) {
    $text = $_REQUEST["text"];
}


$writer->writeFile($text, 'qrcode.png', Encoder::DEFAULT_BYTE_MODE_ECODING, ErrorCorrectionLevel::L);
//$writer->writeFile($text, 'qrcode.png', Encoder::DEFAULT_BYTE_MODE_ECODING, ErrorCorrectionLevel::M);
//$writer->writeFile($text, 'qrcode.png', Encoder::DEFAULT_BYTE_MODE_ECODING, ErrorCorrectionLevel::Q);
//$writer->writeFile($text, 'qrcode.png', Encoder::DEFAULT_BYTE_MODE_ECODING, ErrorCorrectionLevel::H);

// measure speed
$end = microtime(true);
$duration = number_format(($end - $start) * 1000, 3) . "ms";

?>
    <p>Use: /?text=Here goes your text</p>
    <img src="qrcode.png"/>
<!--    <img src="qrcodeM.png"/>-->
<!--    <img src="qrcodeQ.png"/>-->
<!--    <img src="qrcodeH.png"/>-->
<?php echo $duration; ?>