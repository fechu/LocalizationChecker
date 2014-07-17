<?php

namespace SM\Lexer;

use SM\Lexer\Lexer;
use SM\Lexer\Token;

/**
 * @author Sandro Meier
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SM\Lexer\Lexer
     */
    protected $lexer;

    public function setup()
    {
        $this->lexer = new Lexer(array());
    }

    public function testLexerCreatedWithNoTokenDefinitions()
    {
        $this->assertNotNull($this->lexer);
        $this->assertEquals(array(), $this->lexer->getTokenDefinitions());
    }

    public function testLexerDoesNotAcceptInvalidTokenDefinition()
    {
        $this->setExpectedException("InvalidArgumentException");

        // Prepare token definition
        $tokenDef = array(
            new Token("regex", "identifier"),
            "Invalid Token definition"
        );

        $lexer = new Lexer($tokenDef);
    }
}   
