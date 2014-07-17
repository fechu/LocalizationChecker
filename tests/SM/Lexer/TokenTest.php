<?php

namespace SM\Lexer;

use SM\Lexer\Token;

class TokenTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var SM\Lexer\Token
     */
    protected $token;

    public function setup()
    {
        $this->token = new Token("keyword", "TestToken");
    }

    public function testTokenIsCreatedSuccessfully  ()
    {
        $this->assertNotNull($this->token);
    }

    public function testRegexIsSetInConstructor()
    {
        $this->assertEquals("keyword", $this->token->getRegex());
    }

    public function testIdentifierIsSetInConstructor()
    {
        $this->assertEquals("TestToken", $this->token->getIdentifier());
        
    }

    
    public function testSetMatcheSetsMatchLineAndOffset()
    {
        $match = array("matchedKeyword");
        $this->token->setMatch($match, 1, 2);

        $this->assertEquals($match, $this->token->getMatch());
        $this->assertEquals(1, $this->token->getLine());
        $this->assertEquals(2, $this->token->getOffset());
    }
    
    
    
}
