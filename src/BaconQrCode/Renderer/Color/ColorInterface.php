<?php
/**
 * BaconQrCode
 *
 * @link      http://github.com/Bacon/BaconQrCode For the canonical source repository
 * @copyright 2013 Ben 'DASPRiD' Scholzen
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace BaconQrCode\Renderer\Color;

/**
 * Color interface.
 */
abstract class ColorInterface
{
    /**
     * Converts the color to RGB.
     *
     * @return Rgb
     */
    public abstract function toRgb();

    /**
     * Converts the color to CMYK.
     *
     * @return Cmyk
     */
    public abstract function toCmyk();

    /**
     * Converts the color to gray.
     *
     * @return Gray
     */
    public abstract function toGray();

    /**
     * Outputs color as a four component array with RGBA
     * @param bool $asString
     * @return array|string
     */
    public function toRGBA($asString = false)
    {
        $rgb = $this->toRgb();
        if ($asString) {
            return 'rgba(' . $rgb->getRed() . ',' . $rgb->getGreen() . ',' . $rgb->getBlue() . ',1)';
        } else {
            return [$rgb->getRed(), $rgb->getGreen(), $rgb->getBlue(), 1];
        }
    }
}