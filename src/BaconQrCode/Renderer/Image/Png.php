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
    public function drawEllipse($x, $y, $colorId, $radiusSize = 1.82)
    {
        $img = $this->image;
        $radius = ($this->blockSize - 1) / 2;
        $cx = $x + $radius;
        $cy = $y + $radius;
        $fillColor = $this->colors[$colorId];

        imageSmoothArc($img, $cx, $cy, $radius * $radiusSize, $radius * $radiusSize,
            $this->getForegroundColor()->toRGBA(), 0, pi() * 2);
    }


    /**
     * drawEllipse(): defined by RendererInterface.
     *
     * @see    ImageRendererInterface::drawEllipse()
     * @param  integer $x
     * @param  integer $y
     * @param  string $colorId
     * @param int $radiusSize
     * @return void
     */
    public function drawMainCircle($x, $y, $colorId, $radiusSize = 6)
    {
        $img = $this->image;
        $radius = ($this->blockSize - 1) / 2;
        $cx = $x + $radius;
        $cy = $y + $radius;

        imageSmoothArc($img, $cx, $cy, $radius * $radiusSize * 2.5, $radius * $radiusSize * 2.5,
            $this->getForegroundColor()->toRGBA(), 0, pi() * 2);
        imageSmoothArc($img, $cx, $cy, $radius * $radiusSize * 1.8, $radius * $radiusSize * 1.8
            , $this->getBackgroundColor()->toRGBA(), 0, pi() * 2);
        imageSmoothArc($img, $cx, $cy, $radius * $radiusSize * 1.1, $radius * $radiusSize * 1.1,
            [150, 109, 5, 1], 0, pi() * 2);
    }

    /**
     * Returns random dark colors.
     * @param int $darkColorTreshold
     * @return array in RGBA format
     */
    public function getRandomDarkColor($darkColorTreshold = 100)
    {
        $color = [(int)rand(0, $darkColorTreshold), (int)rand(0, $darkColorTreshold), (int)rand(0, $darkColorTreshold), 1];
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
        ob_start();
        imagepng($this->image);
        return ob_get_clean();
    }
}