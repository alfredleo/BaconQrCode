<?php
/**
 * BaconQrCode
 *
 * @link      http://github.com/Bacon/BaconQrCode For the canonical source repository
 * @copyright 2013 Ben 'DASPRiD' Scholzen
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace BaconQrCode\Renderer\Image;

use BaconQrCode\Exception;
use BaconQrCode\Renderer\Color\ColorInterface;

/**
 * PNG backend.
 */
class PngImagick extends AbstractRenderer
{
    /**
     * Image resource used when drawing.
     *
     * @var \ImagickDraw
     */
    protected $image;

    /**
     * Colors used for drawing.
     *
     * @var array
     */
    protected $colors = array();

    /**
     * 3 type of finder figure angle
     * @var array
     */
    private $finderOrientation = [0, 90, -90];

    /**
     * init(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::init()
     * @return void
     */
    public function init()
    {
        $this->image = new \ImagickDraw();
        $strokeColor = new \ImagickPixel($this->getForegroundColor()->toRGBA(true));
        $fillColor = new \ImagickPixel($this->getForegroundColor()->toRGBA(true));
        $this->image->setStrokeColor($strokeColor);
        $this->image->setFillColor($fillColor);
    }

    /**
     * addColor(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::addColor()
     * @param  string $id
     * @param  ColorInterface $color
     * @return void
     * @throws Exception\RuntimeException
     */
    public function addColor($id, ColorInterface $color)
    {
        if ($this->image === null) {
            throw new Exception\RuntimeException('Colors can only be added after init');
        }

        $color = $color->toRgb();

        $this->colors[$id] = $color->toRGBA(true);
    }

    /**
     * drawBackground(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::drawBackground()
     * @param  string $colorId
     * @return void
     */
    public function drawBackground($colorId)
    {
        // Background color is set in init
    }

    /**
     * drawBlock(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::drawBlock()
     * @param  integer $x
     * @param  integer $y
     * @param  string $colorId
     * @return void
     */
    public function drawBlock($x, $y, $colorId = 'foreground')
    {
        $this->image->rectangle($x, $y, $x + $this->blockSize - 1, $y + $this->blockSize - 1);
    }

    /**
     * drawCircle(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::drawCircle()
     * @param  integer $x
     * @param  integer $y
     * @param  string $colorId
     * @param  int $radiusSize
     * @return void
     */
    public function drawCircle($x, $y, $colorId = null, $radiusSize = 0)
    {
        // -1 is used for the right block size, as there is one pixel line between all blocks
        $radius = ($this->blockSize) / 2.0; // -1 is removed to make circles stay more closed to each other
        $cx = $x + $radius;
        $cy = $y + $radius;
//        $this->image->setFillColor($this->getRandomDarkColor());
        $this->image->circle($cx, $cy, $cx, $y);
    }


    /**
     * drawFinderPattern(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::drawFinderPattern()
     * @param  integer $x
     * @param  integer $y
     * @param  int $pointCount size of qr code points
     * @param  string $colorId
     * @param  int $radiusSize
     * @return void
     */
    public function drawFinderPattern($x, $y, $pointCount = 25, $colorId = 'foreground', $radiusSize = 6)
    {
        $radius = ($this->blockSize - 1) / 2;
        $cx = $x + $radius;
        $cy = $y + $radius;
        // get first array value and remove it
        $angle = array_shift($this->finderOrientation);
        $this->drawFinderFigure($cx, $cy, $angle, $radius, $this->image);
    }

    /**
     * Returns random dark colors.
     * @param int $darkColorTreshold
     * @return string in RGBA format
     */
    public function getRandomDarkColor($darkColorTreshold = 100)
    {
        $color = 'rgba(' . (int)rand(0, $darkColorTreshold) . ',' .
            (int)rand(0, $darkColorTreshold) . ',' .
            (int)rand(0, $darkColorTreshold) . ',1)';
        return $color;
    }


    /**
     * getByteStream(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::getByteStream()
     * @return string
     */
    public function getByteStream()
    {
        $imagick = new \Imagick();
        $imagick->newImage($this->finalWidth, $this->finalHeight, $this->getBackgroundColor()->toRGBA(true));
        $imagick->setImageFormat("png");
        // Render the draw commands in the ImagickDraw object into the image.
        $imagick->drawImage($this->image);

        // Send the image to the browser
        ob_start();
        echo $imagick->getImageBlob();
        return ob_get_clean();
    }


    /**
     * Draws custom figure
     * @param $offsetX
     * @param $offsetY
     * @param $rotate
     * @param $pointRadius float radius of small circles in qr code
     * @param $draw \ImagickDraw
     */
    function drawFinderFigure(
        $offsetX,
        $offsetY,
        $rotate,
        $pointRadius,
        $draw
    ) {
        /** 3 quarters circle radius, 3 is the empty lines offset */
        $r = 6 * $pointRadius + 3;
        /** stroke width */
        $strokeW = $pointRadius * 2;
        /** used to remove artifacts on high stroke width, on 0 shows artifacts */
        $ra = 0.01;
        /** remove artifacts for small quarter circle, on 1 shows artifacts */
        $ras = 1.1;
        /** bezier quarter circle constant*/
        $BCC = 0.552284749831;

        $draw->translate($offsetX, $offsetY);
        $draw->rotate($rotate);
        $draw->setStrokeWidth($strokeW);
        $draw->setFillOpacity(0);
        $draw->setStrokeOpacity(1);

        $smoothPointsSet = [
            [ // first quarter circle
                ['x' => 0, 'y' => -$r],
                ['x' => $r * $BCC, 'y' => -$r],
                ['x' => $r, 'y' => -$r * $BCC],
                ['x' => $r, 'y' => 0],
            ],
            [ // second quarter circle
                ['x' => $r, 'y' => -$r * $ra],
                ['x' => $r, 'y' => $r * $BCC],
                ['x' => $r * $BCC, 'y' => $r],
                ['x' => -$r * $ra, 'y' => $r],
            ],
            [ // third quarter circle
                ['x' => 0, 'y' => $r],
                ['x' => -$r * $BCC, 'y' => $r],
                ['x' => -$r, 'y' => $r * $BCC],
                ['x' => -$r, 'y' => 0],
            ],
            [ // fourth small quarter circle
                ['x' => -$r, 'y' => -$r + $pointRadius * $ras],
                ['x' => -$r, 'y' => -$r + $pointRadius * (1 - $BCC)],
                ['x' => -$r + $pointRadius * (1 - $BCC), 'y' => -$r],
                ['x' => -$r + $pointRadius * $ras, 'y' => -$r],
            ],
        ];
        // two straight lines
        $draw->line(-$r, 0, -$r, -$r + $pointRadius);
        $draw->line(0, -$r, -$r + $pointRadius, -$r);

        foreach ($smoothPointsSet as $points) {
            $draw->bezier($points);
        }

        // draw middle circle
        $draw->setFillOpacity(1);
        $draw->setStrokeOpacity(0);
        $fillColor = $draw->getFillColor();
        $draw->setFillColor($this->getFinderColor()->toRGBA(true));
        // +1 is the empty line offset
        $draw->circle(0, 0, 0, 3 * $pointRadius + 1);
        $draw->setFillColor($fillColor);

        // reset coordinate center and rotation
        $draw->rotate(-$rotate);
        $draw->translate(-$offsetX, -$offsetY);
    }
}