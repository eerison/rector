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

/**
 * Formatter interface for console output.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface OutputFormatterInterface
{
    /**
     * Sets the decorated flag.
     */
    public function setDecorated(bool $decorated);
    /**
     * Whether the output will decorate messages.
     */
    public function isDecorated() : bool;
    /**
     * Sets a new style.
     */
    public function setStyle(string $name, \RectorPrefix20220404\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface $style);
    /**
     * Checks if output formatter has style with specified name.
     */
    public function hasStyle(string $name) : bool;
    /**
     * Gets style options from style with specified name.
     *
     * @throws \InvalidArgumentException When style isn't defined
     */
    public function getStyle(string $name) : \RectorPrefix20220404\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;
    /**
     * Formats a message according to the given styles.
     */
    public function format(?string $message) : ?string;
}
