<?php
/**
 * Created by PhpStorm.
 * User: Alfred
 * Date: 02.11.2017
 * Time: 21:35
 */

// for algorithms on pure gd with good antialias see http://create.stephan-brumme.com/antialiased-circle/

require(__DIR__ . '/vendor/autoload.php');
include_once(__DIR__ . '/imageSmoothArc.php');
//include_once('C:\wamp64\helpers\dumpphp\dumping.php');

$start = microtime(true);
$renderer = new \BaconQrCode\Renderer\Image\Png();
$renderer->setHeight(800);
$renderer->setWidth(800);
$writer = new \BaconQrCode\Writer($renderer);
$text = 'http://www.sabgames.org';
if (isset($_REQUEST["text"]) && count($_REQUEST["text"]) > 0)
    $text = $_REQUEST["text"];

$writer->writeFile($text, 'qrcode.png');

// measure speed
$end = microtime(true);
$duration = number_format(($end - $start) * 1000, 3) . "ms";

?>
<p>Use: /?text=Here goes your text</p>
    <img src="qrcode.png"/>
<?php echo $duration; ?>