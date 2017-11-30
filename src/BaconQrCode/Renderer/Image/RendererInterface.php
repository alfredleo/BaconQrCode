<?php
/**
 * BaconQrCode
 *
 * @link      http://github.com/Bacon/BaconQrCode For the canonical source repository
 * @copyright 2013 Ben 'DASPRiD' Scholzen
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace BaconQrCode\Renderer\Image;

use BaconQrCode\Renderer\Color\ColorInterface;
use BaconQrCode\Renderer\RendererInterface as GeneralRendererInterface;

/**
 * Renderer interface.
 */
interface RendererInterface extends GeneralRendererInterface
{
    /**
     * Initiates the drawing area.
     *
     * @return void
     */
    public function init();

    /**
     * Adds a color to the drawing area.
     *
     * @param  string $id
     * @param  ColorInterface $color
     * @return void
     */
    public function addColor($id, ColorInterface $color);

    /**
     * Draws the background.
     *
     * @param  string $colorId
     * @return void
     */
    public function drawBackground($colorId);

    /**
     * Draws a block at a specified position.
     *
     * @param  integer $x
     * @param  integer $y
     * @param  string $colorId
     * @return void
     */
    public function drawBlock($x, $y, $colorId = 'foreground');

    /**
     * Draws a circle at a specified position
     * @param $x
     * @param $y
     * @param string $colorId
     * @param int $radiusSize
     * @return void
     */
    public function drawCircle($x, $y, $colorId = 'foreground', $radiusSize = 1);

    /**
     * Draws finder pattern at specified position.
     * @param $x
     * @param $y
     * @param int $pointCount
     * @param $colorId
     * @param int $radiusSize
     * @return void
     */
    public function drawFinderPattern($x, $y, $pointCount = 25, $colorId = 'foreground', $radiusSize = 1);

    /**
     * Draws custom logo in the $x, $y coordinates of qr code of size $logosize
     * @param $x
     * @param $y
     * @param int $logoSize
     * @return mixed
     */
    public function drawLogo($x, $y, $logoSize = 5);

    /**
     * Returns the byte stream representing the QR code.
     *
     * @return string
     */
    public function getByteStream();

}
