<?php
namespace Speedy;

/**
 * The core of SpeedyBBC, this class will translate text into a formatted message.
 *
 * @package Speedy
 */
class SpeedyBBC
{
    /**
     * Parses the text into a formatted HTML message. It is assumed that the text supplied has already been passed
     * through the {@link http://www.php.net/htmlspecialchars} function.
     *
     * @param string $text The original text to parse.
     */
    public function parse($text)
    {
        $speedyLexer = new SpeedyLexer();
        $speedyParser = new SpeedyParser();
    }
} 