<?php
namespace Speedy\Token;

/**
 * This class represents a token, as generated by the lexer.
 *
 * @package Speedy
 */
abstract class SpeedyToken
{
    /**
     * A text token.
     */
    const TYPE_TEXT = 1;

    /**
     * A tag token.
     */
    const TYPE_TAG = 2;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $type;

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
} 