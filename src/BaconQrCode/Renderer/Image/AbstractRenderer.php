<?php
/**
 * BaconQrCode
 *
 * @link      http://github.com/Bacon/BaconQrCode For the canonical source repository
 * @copyright 2013 Ben 'DASPRiD' Scholzen
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace BaconQrCode\Renderer\Image;

use BaconQrCode\Encoder\QrCode;
use BaconQrCode\Renderer\Color;
use BaconQrCode\Renderer\Image\Decorator\DecoratorInterface;
use BaconQrCode\Exception;

/**
 * Image renderer, supporting multiple backends.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * Margin around the QR code, also known as quiet zone.
     *
     * @var integer
     */
    protected $margin = 4;

    /**
     * Requested width of the rendered image.
     *
     * @var integer
     */
    protected $width = 0;

    /**
     * Requested height of the rendered image.
     *
     * @var integer
     */
    protected $height = 0;

    /**
     * Whether dimensions should be rounded down.
     *
     * @var boolean
     */
    protected $roundDimensions = true;

    /**
     * Final width of the image.
     *
     * @var integer
     */
    protected $finalWidth;

    /**
     * Final height of the image.
     *
     * @var integer
     */
    protected $finalHeight;

    /**
     * Size of each individual block.
     *
     * @var integer
     */
    protected $blockSize;

    /**
     * Background color.
     *
     * @var Color\ColorInterface
     */
    protected $backgroundColor;

    /**
     * Finder eye color.
     *
     * @var Color\ColorInterface
     */
    protected $finderEyeColor;

    /**
     * Finder color.
     *
     * @var Color\ColorInterface
     */
    protected $finderColor;

    /**
     * Whether dimensions should be rounded down
     *
     * @var boolean
     */
    protected $floorToClosestDimension;

    /**
     * Foreground color.
     *
     * @var Color\ColorInterface
     */
    protected $foregroundColor;

    /**
     * Decorators used on QR codes.
     *
     * @var array
     */
    protected $decorators = array();

    /**
     * Sets the margin around the QR code.
     *
     * @param  integer $margin
     * @return AbstractRenderer
     * @throws Exception\InvalidArgumentException
     */
    public function setMargin($margin)
    {
        if ($margin < 0) {
            throw new Exception\InvalidArgumentException('Margin must be equal to greater than 0');
        }

        $this->margin = (int)$margin;
        return $this;
    }

    /**
     * Gets the margin around the QR code.
     *
     * @return integer
     */
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * Sets the height around the rendered image.
     *
     * If the width is smaller than the matrix width plus padding, the renderer
     * will automatically use that as the width instead of the specified one.
     *
     * @param  integer $width
     * @return AbstractRenderer
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;
        return $this;
    }

    /**
     * Gets the width of the rendered image.
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the height around the renderd image.
     *
     * If the height is smaller than the matrix height plus padding, the
     * renderer will automatically use that as the height instead of the
     * specified one.
     *
     * @param  integer $height
     * @return AbstractRenderer
     */
    public function setHeight($height)
    {
        $this->height = (int)$height;
        return $this;
    }

    /**
     * Gets the height around the rendered image.
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets whether dimensions should be rounded down.
     *
     * @param  boolean $flag
     * @return AbstractRenderer
     */
    public function setRoundDimensions($flag)
    {
        $this->floorToClosestDimension = $flag;
        return $this;
    }

    /**
     * Gets whether dimensions should be rounded down.
     *
     * @return boolean
     */
    public function shouldRoundDimensions()
    {
        return $this->floorToClosestDimension;
    }

    /**
     * Sets background color.
     *
     * @param  Color\ColorInterface $color
     * @return AbstractRenderer
     */
    public function setBackgroundColor(Color\ColorInterface $color)
    {
        $this->backgroundColor = $color;
        return $this;
    }

    /**
     * Gets background color.
     *
     * @return Color\ColorInterface
     */
    public function getBackgroundColor()
    {
        if ($this->backgroundColor === null) {
            $this->backgroundColor = new Color\Gray(100);
        }

        return $this->backgroundColor;
    }

    /**
     * Sets foreground color.
     *
     * @param  Color\ColorInterface $color
     * @return AbstractRenderer
     */
    public function setForegroundColor(Color\ColorInterface $color)
    {
        $this->foregroundColor = $color;
        return $this;
    }

    /**
     * Gets foreground color.
     *
     * @return Color\ColorInterface
     */
    public function getForegroundColor()
    {
        if ($this->foregroundColor === null) {
            $this->foregroundColor = new Color\Gray(0);
        }

        return $this->foregroundColor;
    }

    /**
     * Adds a decorator to the renderer.
     *
     * @param  DecoratorInterface $decorator
     * @return AbstractRenderer
     */
    public function addDecorator(DecoratorInterface $decorator)
    {
        $this->decorators[] = $decorator;
        return $this;
    }

    /**
     * render(): defined by RendererInterface.
     *
     * @see    RendererInterface::render()
     * @param  QrCode $qrCode
     * @return string
     */
    public function render(QrCode $qrCode)
    {
        $input = $qrCode->getMatrix();
        $inputWidth = $input->getWidth();
        $inputHeight = $input->getHeight();
        $qrWidth = $inputWidth + ($this->getMargin() << 1);
        $qrHeight = $inputHeight + ($this->getMargin() << 1);
        $outputWidth = max($this->getWidth(), $qrWidth);
        $outputHeight = max($this->getHeight(), $qrHeight);
        $multiple = (int)min($outputWidth / $qrWidth, $outputHeight / $qrHeight);

        if ($this->shouldRoundDimensions()) {
            $outputWidth -= $outputWidth % $multiple;
            $outputHeight -= $outputHeight % $multiple;
        }

        // Padding includes both the quiet zone and the extra white pixels to
        // accommodate the requested dimensions. For example, if input is 25x25
        // the QR will be 33x33 including the quiet zone. If the requested size
        // is 200x160, the multiple will be 4, for a QR of 132x132. These will
        // handle all the padding from 100x100 (the actual QR) up to 200x160.
        $leftPadding = (int)(($outputWidth - ($inputWidth * $multiple)) / 2);
        $topPadding = (int)(($outputHeight - ($inputHeight * $multiple)) / 2);

        // Store calculated parameters
        $this->finalWidth = $outputWidth;
        $this->finalHeight = $outputHeight;
        $this->blockSize = $multiple;

        $this->init();
        $this->addColor('background', $this->getBackgroundColor());
        $this->addColor('foreground', $this->getForegroundColor());
        $this->drawBackground('background');

        foreach ($this->decorators as $decorator) {
            $decorator->preProcess(
                $qrCode,
                $this,
                $outputWidth,
                $outputHeight,
                $leftPadding,
                $topPadding,
                $multiple
            );
        }

        // remove circles for 3 finder parts of 7x7 size
        $mainBlocksize = 7;
        $removedPoints = [];
        $size = $input->getWidth();
        for ($i = 0; $i < $mainBlocksize; $i++) {
            for ($j = 0; $j < $mainBlocksize; $j++) {
                $removedPoints[$i][] = $j;
                $removedPoints[$i][] = $size - $j - 1;
                $removedPoints[$size - $i - 1][] = $j;
            }
        }
        // remove 5x5 center circles to add custom logo.
        $logoSize = 5;
        $startPoint = (int)(($size - $logoSize) / 2.0);
        for ($i = 0; $i < $logoSize; $i++) {
            for ($j = 0; $j < $logoSize; $j++) {
                $removedPoints[$startPoint + $i][] = $startPoint + $j;
            }
        }
        // logo center coordinates
        $logoCenter = $startPoint + (int)($logoSize / 2.0) + 1;

        // Set 3 center points of Main squares.
        $mainSquares = [3 => [3, $size - 4], ($size - 4) => [3]];
        // Main rendering routine.
        for ($inputY = 0, $outputY = $topPadding; $inputY < $inputHeight; $inputY++, $outputY += $multiple) {
            for ($inputX = 0, $outputX = $leftPadding; $inputX < $inputWidth; $inputX++, $outputX += $multiple) {
                if ($input->get($inputX, $inputY) === 1) {
                    // here we removing 7x7 3 Position squares.
                    if (!(isset($removedPoints[$inputX]) && in_array($inputY, $removedPoints[$inputX], true))) {
//                        $this->drawBlock($outputX, $outputY);
                        $this->drawCircle($outputX, $outputY);
                    }
                    if (isset($mainSquares[$inputX]) && in_array($inputY, $mainSquares[$inputX])) {
                        $this->drawFinderPattern($outputX, $outputY, $size);
                    }
                }
                if ($inputX == $logoCenter && $inputY == $logoCenter) {
                    $this->drawLogo($outputX, $outputY, $logoSize);
                }
            }
        }


        foreach ($this->decorators as $decorator) {
            $decorator->postProcess(
                $qrCode,
                $this,
                $outputWidth,
                $outputHeight,
                $leftPadding,
                $topPadding,
                $multiple
            );
        }

        return $this->getByteStream();
    }

    /**
     * Fluent setter
     * @param Color\ColorInterface $finderColor
     * @return $this
     */
    public function setFinderEyeColor($finderColor)
    {
        $this->finderEyeColor = $finderColor;
        return $this;
    }

    /**
     * Fluent setter
     * @param Color\ColorInterface $finderColor
     * @return $this
     */
    public function setFinderColor($finderColor)
    {
        $this->finderColor = $finderColor;
        return $this;
    }

    /**
     * @return Color\ColorInterface|Color\Gray
     */
    public function getFinderColor()
    {
        if ($this->finderColor === null) {
            $this->finderColor = new Color\Gray(100);
        }
        return $this->finderColor;
    }

    /**
     * @return Color\ColorInterface|Color\Gray
     */
    public function getFinderEyeColor()
    {
        if ($this->finderEyeColor === null) {
            $this->finderEyeColor = new Color\Gray(100);
        }
        return $this->finderEyeColor;
    }
}