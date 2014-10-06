<?php
namespace SpeedyTest;
use Speedy\SpeedyLexer;

/**
 * Class SpeedyLexerTest
 *
 * @package SpeedyTest
 */
class SpeedyLexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Speedy\SpeedyLexer
     */
    private $lexer;

    /**
     * Sets up the lexer for testing.
     */
    public function setUp()
    {
        $this->lexer = new SpeedyLexer();
    }

    /**
     * Tests the lexer's ability to find the ending of a quote block.
     */
    public function testGetQuoteEndingPosition()
    {
        $text = 'a &quot;quote&quot;';
        $quote = '&quot;';
        $sq_pos = strpos($text, $quote);
        $eq_pos = strrpos($text, $quote);

        $position = $this->lexer->getQuoteEndingPosition($text, $sq_pos, $quote);

        $this->assertTrue(is_array($position));
        $this->assertEquals(2, count($position));
        $this->assertEquals($sq_pos, $position[0]);
        $this->assertEquals($eq_pos, $position[1]);
    }

    /**
     * Tests the lexer's ability to find the ending of a quote block when there are quotes escaped.
     */
    public function testGetQuoteEndingPositionWithEscapedQuote()
    {
        $text = 'a &#039;quote\&#039;&#039;';
        $quote = '&#039;';
        $sq_pos = strpos($text, $quote);
        $eq_pos = strrpos($text, $quote);

        $position = $this->lexer->getQuoteEndingPosition($text, $sq_pos, $quote);

        $this->assertTrue(is_array($position));
        $this->assertEquals(2, count($position));
        $this->assertEquals($sq_pos, $position[0]);
        $this->assertEquals($eq_pos, $position[1]);
    }

    public function testGetBracketEndingPosition()
    {
        $text = 'this is [a] tag';
        $text_length = strlen($text);
        $obrk_pos = strpos($text, '[');
        $cbrk_pos = strrpos($text, ']');

        $position = $this->lexer->getBracketEndingPos($text, $text_length, $obrk_pos, $cbrk_pos);

        $this->assertTrue(is_array($position));
        $this->assertEquals(2, count($position));
        $this->assertEquals($cbrk_pos, $position[0]);
        $this->assertEquals(array(), $position[1]);
    }

    public function testGetBracketEndingPositionWithQuotes()
    {
        $text = 'click... [url=&quot;www.github.com&quot;]here';
        $text_length = strlen($text);
        $obrk_pos = strpos($text, '[');
        $cbrk_pos = strrpos($text, ']');

        $quote = '&quot;';
        $sq_pos = strpos($text, $quote);
        $eq_pos = strrpos($text, $quote);

        $position = $this->lexer->getBracketEndingPos($text, $text_length, $obrk_pos, $cbrk_pos);

        $this->assertTrue(is_array($position));
        $this->assertEquals(2, count($position));
        $this->assertEquals($cbrk_pos, $position[0]);
        $this->assertEquals(array(array($sq_pos, $eq_pos)), $position[1]);
    }
}
 