<?php
namespace Speedy;
use Speedy\Token\SpeedyTagToken;
use Speedy\Token\SpeedyTextToken;
use Speedy\Token\SpeedyToken;

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

    const ESCAPE_CHAR = '\\';

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
        $cur_pos = 0;
        $text_length = strlen($text);
        while ($cur_pos < $text_length)
        {
            $obrk_pos = strpos($text, self::OPEN_BRACKET, $cur_pos);

            // If there are no more opening brackets, we're done.
            if ($obrk_pos === false)
            {
                // Get the remaining text.
                $rem_text = substr($text, $cur_pos);
                $tokens[] = $this->getTextToken($rem_text);

                break;
            }

            $cbrk_pos = strpos($text, self::CLOSE_BRACKET, $obrk_pos);

            // Now we must check for a closing bracket.
            if ($cbrk_pos === false)
            {
                $rem_text = substr($text, $cur_pos);
                $tokens[] = $this->getTextToken($rem_text);

                break;
            }

            // Now that we know there is the chance of this being a tag, we need to figure out it's true ending.
            $rcbrk_pos = $this->getBracketEndingPos($text, $text_length, $obrk_pos, $cbrk_pos);

            // Before we get the tag token, we might need to make a text token of the in-between text.
            if ($obrk_pos > $cur_pos)
            {
                $rem_text = substr($text, $cur_pos, $obrk_pos - $cbrk_pos);
                $tokens[] = $this->getTextToken($rem_text);
            }

            // Okay, now get the tag token.
            $tokens[] = $this->getTagToken(substr($text, $obrk_pos, $rcbrk_pos[0]  -$obrk_pos), $rcbrk_pos[1]);

            // Move the current position.
            $cur_pos = $rcbrk_pos + 1;
        }

        return $tokens;
    }

    /**
     * @param $text
     * @return SpeedyToken
     */
    private function getTextToken($text)
    {
        $token = new SpeedyTextToken($text);

        return $token;
    }

    /**
     * @param $text
     * @param array $quote_positions
     * @return \Speedy\Token\SpeedyTagToken
     */
    private function getTagToken($text, array $quote_positions)
    {
        return new SpeedyTagToken($text, $quote_positions);
    }

    /**
     * @param string $text
     * @param $text_length
     * @param int $obrk_pos
     * @param int $cbrk_pos
     * @return array An array, with the first index being the bracket ending position within the string and the second
     *               being an array of starting and ending positions of quotes (each entry is an array with two points,
     *               the starting position of the quote and the ending).
     */
    private function getBracketEndingPos($text, $text_length, $obrk_pos, $cbrk_pos)
    {
        // The goal of this method is pretty simple and straightforward: to find the closing bracket of the tag. However
        // the simplicity of this can become more complicated if there are any quotes between the opening and closing
        // bracket which will then require a character by character verification to ensure that bracket isn't within a
        // quote.
        if (strpos($text, self::DOUBLE_QUOTE, $obrk_pos) === false || strpos($text, self::SINGLE_QUOTE, $obrk_pos) === false)
        {
            return array($cbrk_pos, array());
        }

        // We will start right after the opening bracket.
        $cur_pos = $obrk_pos + 1;
        $quote_positions = array();
        while ($cur_pos < $text_length)
        {
            $dbl_pos = strpos($text, self::DOUBLE_QUOTE, $cur_pos);
            $sngl_pos = strpos($text, self::SINGLE_QUOTE, $cur_pos);
            $cbrk_pos = strpos($text, self::CLOSE_BRACKET, $cur_pos);

            if ($dbl_pos === false) {
                $dbl_pos = $text_length;
            }

            if ($sngl_pos === false) {
                $sngl_pos = $text_length;
            }

            // If the closing bracket is before any other quotes then we're done.
            if ($cbrk_pos < $dbl_pos && $cbrk_pos < $sngl_pos)
            {
                return array($cbrk_pos, $quote_positions);
            }

            $q_pos = min($dbl_pos, $sngl_pos);
            $quote = $q_pos == $dbl_pos ? self::DOUBLE_QUOTE : self::SINGLE_QUOTE;
            $position = $this->getQuoteEndingPosition($text, $q_pos, $quote);

            // Add the point, then set the current position right after the ending quote.
            $quote_positions[] = $position;
            $cur_pos = $position[1] + strlen($quote);
        }

        return array(0, $quote_positions);
    }

    /**
     * @param string $text
     * @param int $sq_pos
     * @param string $quote
     * @return array
     */
    private function getQuoteEndingPosition($text, $sq_pos, $quote)
    {
        // The ending quote position... unknown now (so use the opening quote).
        $eq_pos = $sq_pos;
        $cur_pos = $sq_pos + 1;
        while (($nq_pos = strpos($text, $quote, $cur_pos)) !== false)
        {
            // We need to see if it is escaped.
            $prev_char = $text[$nq_pos - 1];

            if ($prev_char == self::ESCAPE_CHAR)
            {
                $cur_pos = $nq_pos + strlen($quote);
                continue;
            }

            $eq_pos = $cur_pos;
            break;
        }

        return array($sq_pos, $eq_pos);
    }
} 