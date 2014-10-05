<?php
namespace Speedy\Token;

/**
 * A token representing plain text.
 *
 * @package Speedy\Token
 */
class SpeedyTextToken extends SpeedyToken
{
    /**
     * @param string $text
     */
    public function __construct($text = null)
    {
        $this->setType(self::TYPE_TEXT);
        $this->setText($text);
    }
} 