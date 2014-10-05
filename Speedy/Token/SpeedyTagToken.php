<?php
namespace Speedy\Token;

/**
 * This is a token representing a tag, offering additional functionality just for tags.
 *
 * @package Speedy\Token
 */
class SpeedyTagToken extends SpeedyToken
{
    private $quote_positions;

    public function __construct($text, array $quote_positions)
    {
        $this->setType(self::TYPE_TAG);
        $this->setText($text);
        $this->setQuotePositions($quote_positions);
    }

    private function setQuotePositions($quote_positions)
    {
        $this->quote_positions = $quote_positions;
    }
} 