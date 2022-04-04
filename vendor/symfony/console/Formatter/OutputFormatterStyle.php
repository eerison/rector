<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix20220404\Symfony\Component\Console\Formatter;

use RectorPrefix20220404\Symfony\Component\Console\Color;
/**
 * Formatter style class for defining styles.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class OutputFormatterStyle implements \RectorPrefix20220404\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
{
    private $color;
    /**
     * @var string
     */
    private $foreground;
    /**
     * @var string
     */
    private $background;
    /**
     * @var mixed[]
     */
    private $options;
    /**
     * @var string|null
     */
    private $href;
    /**
     * @var bool
     */
    private $handlesHrefGracefully;
    /**
     * Initializes output formatter style.
     *
     * @param string|null $foreground The style foreground color name
     * @param string|null $background The style background color name
     */
    public function __construct(string $foreground = null, string $background = null, array $options = [])
    {
        $this->color = new \RectorPrefix20220404\Symfony\Component\Console\Color($this->foreground = $foreground ?: '', $this->background = $background ?: '', $this->options = $options);
    }
    /**
     * {@inheritdoc}
     */
    public function setForeground(string $color = null)
    {
        $this->color = new \RectorPrefix20220404\Symfony\Component\Console\Color($this->foreground = $color ?: '', $this->background, $this->options);
    }
    /**
     * {@inheritdoc}
     */
    public function setBackground(string $color = null)
    {
        $this->color = new \RectorPrefix20220404\Symfony\Component\Console\Color($this->foreground, $this->background = $color ?: '', $this->options);
    }
    public function setHref(string $url) : void
    {
        $this->href = $url;
    }
    /**
     * {@inheritdoc}
     */
    public function setOption(string $option)
    {
        $this->options[] = $option;
        $this->color = new \RectorPrefix20220404\Symfony\Component\Console\Color($this->foreground, $this->background, $this->options);
    }
    /**
     * {@inheritdoc}
     */
    public function unsetOption(string $option)
    {
        $pos = \array_search($option, $this->options);
        if (\false !== $pos) {
            unset($this->options[$pos]);
        }
        $this->color = new \RectorPrefix20220404\Symfony\Component\Console\Color($this->foreground, $this->background, $this->options);
    }
    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->color = new \RectorPrefix20220404\Symfony\Component\Console\Color($this->foreground, $this->background, $this->options = $options);
    }
    /**
     * {@inheritdoc}
     */
    public function apply(string $text) : string
    {
        $this->handlesHrefGracefully = $this->handlesHrefGracefully ?? 'JetBrains-JediTerm' !== \getenv('TERMINAL_EMULATOR') && (!\getenv('KONSOLE_VERSION') || (int) \getenv('KONSOLE_VERSION') > 201100);
        if (null !== $this->href && $this->handlesHrefGracefully) {
            $text = "\33]8;;{$this->href}\33\\{$text}\33]8;;\33\\";
        }
        return $this->color->apply($text);
    }
}
