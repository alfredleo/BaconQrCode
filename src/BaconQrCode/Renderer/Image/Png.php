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
class Png extends AbstractRenderer
{
    /**
     * Image resource used when drawing.
     *
     * @var resource
     */
    protected $image;

    /**
     * Colors used for drawing.
     *
     * @var array
     */
    protected $colors = array();

    /**
     * init(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::init()
     * @return void
     */
    public function init()
    {
        $this->image = imagecreatetruecolor($this->finalWidth, $this->finalHeight);
//        imageantialias($this->image, true);
//        imagealphablending($this->image, true);
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

        $this->colors[$id] = imagecolorallocate(
            $this->image,
            $color->getRed(),
            $color->getGreen(),
            $color->getBlue()
        );
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
        imagefill($this->image, 0, 0, $this->colors[$colorId]);
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
    public function drawBlock($x, $y, $colorId)
    {
        $this->drawEllipse($x, $y, $colorId);
        return;
        imagefilledrectangle(
            $this->image,
            $x,
            $y,
            $x + $this->blockSize - 1,
            $y + $this->blockSize - 1,
            $this->colors[$colorId]
        );
    }

    /**
     * drawEllipse(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::drawEllipse()
     * @param  integer $x
     * @param  integer $y
     * @param  string $colorId
     * @return void
     */
    public function drawEllipse($x, $y, $colorId)
    {
        $img = $this->image;
        $radius = ($this->blockSize - 1) / 2;
        $cx = $x + $radius;
        $cy = $y + $radius;
        $fillColor = $this->colors[$colorId];

        /*imagefilledellipse(
            $this->image,
            $cx,
            $cy,
            $radius,
            $radius,
            $fillColor
        );*/

        imageSmoothArc($img, $cx, $cy, $radius, $radius, $fillColor, 0, pi() * 2);
        //        $this->imageSmoothCircle($img, $cx, $cy, $radius, $fillColor);
    }

    public function imageSmoothCircle(&$img, $cx, $cy, $cr, $color)
    {
        $ir = $cr;
        $ix = 0;
        $iy = $ir;
        $ig = 2 * $ir - 3;
        $idgr = -6;
        $idgd = 4 * $ir - 10;
        $fill = imageColorExactAlpha($img, $color['R'], $color['G'], $color['B'], 0);
        imageLine($img, $cx + $cr - 1, $cy, $cx, $cy, $fill);
        imageLine($img, $cx - $cr + 1, $cy, $cx - 1, $cy, $fill);
        imageLine($img, $cx, $cy + $cr - 1, $cx, $cy + 1, $fill);
        imageLine($img, $cx, $cy - $cr + 1, $cx, $cy - 1, $fill);
        $draw = imageColorExactAlpha($img, $color['R'], $color['G'], $color['B'], 42);
        imageSetPixel($img, $cx + $cr, $cy, $draw);
        imageSetPixel($img, $cx - $cr, $cy, $draw);
        imageSetPixel($img, $cx, $cy + $cr, $draw);
        imageSetPixel($img, $cx, $cy - $cr, $draw);
        while ($ix <= $iy - 2) {
            if ($ig < 0) {
                $ig += $idgd;
                $idgd -= 8;
                $iy--;
            } else {
                $ig += $idgr;
                $idgd -= 4;
            }
            $idgr -= 4;
            $ix++;
            imageLine($img, $cx + $ix, $cy + $iy - 1, $cx + $ix, $cy + $ix, $fill);
            imageLine($img, $cx + $ix, $cy - $iy + 1, $cx + $ix, $cy - $ix, $fill);
            imageLine($img, $cx - $ix, $cy + $iy - 1, $cx - $ix, $cy + $ix, $fill);
            imageLine($img, $cx - $ix, $cy - $iy + 1, $cx - $ix, $cy - $ix, $fill);
            imageLine($img, $cx + $iy - 1, $cy + $ix, $cx + $ix, $cy + $ix, $fill);
            imageLine($img, $cx + $iy - 1, $cy - $ix, $cx + $ix, $cy - $ix, $fill);
            imageLine($img, $cx - $iy + 1, $cy + $ix, $cx - $ix, $cy + $ix, $fill);
            imageLine($img, $cx - $iy + 1, $cy - $ix, $cx - $ix, $cy - $ix, $fill);
            $filled = 0;
            for ($xx = $ix - 0.45; $xx < $ix + 0.5; $xx += 0.2) {
                for ($yy = $iy - 0.45; $yy < $iy + 0.5; $yy += 0.2) {
                    if (sqrt(pow($xx, 2) + pow($yy, 2)) < $cr) $filled += 4;
                }
            }
            $draw = imageColorExactAlpha($img, $color['R'], $color['G'], $color['B'], (100 - $filled));
            imageSetPixel($img, $cx + $ix, $cy + $iy, $draw);
            imageSetPixel($img, $cx + $ix, $cy - $iy, $draw);
            imageSetPixel($img, $cx - $ix, $cy + $iy, $draw);
            imageSetPixel($img, $cx - $ix, $cy - $iy, $draw);
            imageSetPixel($img, $cx + $iy, $cy + $ix, $draw);
            imageSetPixel($img, $cx + $iy, $cy - $ix, $draw);
            imageSetPixel($img, $cx - $iy, $cy + $ix, $draw);
            imageSetPixel($img, $cx - $iy, $cy - $ix, $draw);
        }
    }

    /**
     * getByteStream(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::getByteStream()
     * @return string
     */
    public function getByteStream()
    {
        ob_start();
        imagepng($this->image);
        return ob_get_clean();
    }
}