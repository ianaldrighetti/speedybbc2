<?php
namespace Speedy;

/**
 * The Speedy Lexer's job is to translate text into tokens which are then interpreted by the SpeedyParser.
 *
 * @package Speedy
 */
class SpeedyLexer
{
    const OPEN_BRACKET = '[';

    const CLOSE_BRACKET = ']';

    const SINGLE_QUOTE = '&#039;';

    const DOUBLE_QUOTE = '&quot;';

    /**
     * @param string $text
     * @return SpeedyToken[]
     */
    public function tokenize($text)
    {
        $tokens = array();

        // If there are no signs of markup, we won't do anything.
        if (strpos($text, self::OPEN_BRACKET) === false)
        {
            return array($this->getTextToken($text));
        }

        // Otherwise we may need to actually do something.
        $start_pos = 0;
        $text_length = strlen($text);
        while ($start_pos < $text_length)
        {
            $obrk_pos = strpos($text, self::OPEN_BRACKET, $start_pos);

            // If there are no more opening brackets, we're done.
            if ($obrk_pos === false)
            {
                // Get the remaining text.
                $rem_text = substr($text, $start_pos);
                $tokens[] = $this->getTextToken($rem_text);

                break;
            }

            $cbrk_pos = strpos($text, self::CLOSE_BRACKET, $obrk_pos);

            // Now we must check for a closing bracket.
            if ($cbrk_pos === false)
            {
                $rem_text = substr($text, $start_pos);
                $tokens[] = $this->getTextToken($rem_text);

                break;
            }

            // Now that we know there is the chance of this being a tag, we need to figure out it's true ending.
            $rcbrk_pos = $this->getBracketEndingPos($text, $text_length, $obrk_pos, $cbrk_pos);
        }

        return $tokens;
    }

    /**
     * @param $text
     * @return SpeedyToken
     */
    private function getTextToken($text)
    {
        $token = new SpeedyToken();
        $token->setText($text);
        $token->setType(SpeedyToken::TYPE_TEXT);

        return $token;
    }

    /**
     * @param string $text
     * @param int $obrk_pos
     * @param int $cbrk_pos
     * @return int
     */
    private function getBracketEndingPos($text, $text_length, $obrk_pos, $cbrk_pos)
    {
        // The goal of this method is pretty simple and straightforward: to find the closing bracket of the tag. However
        // the simplicity of this can become more complicated if there are any quotes between the opening and closing
        // bracket which will then require a character by character verification to ensure that bracket isn't within a
        // quote.

        if (strpos($text, self::DOUBLE_QUOTE, $obrk_pos) !== false || strpos($text, self::SINGLE_QUOTE, $obrk_pos) !== false)
        {
            return strpos($text, self::CLOSE_BRACKET, $obrk_pos);
        }

        // We will start right after the opening bracket.
        $cur_pos = $obrk_pos + 1;
        while ($cur_pos < $text_length)
        {
            $char = $text[$cur_pos];
        }

        return 0;
    }
} 