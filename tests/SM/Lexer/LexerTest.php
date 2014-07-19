<?php

namespace SM\Lexer;

use SM\Lexer\Lexer;
use SM\Lexer\Token;
use SM\Lexer\CustomToken;

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

    public function testLexerHasNoErrorsByDefault()
    {
        $this->assertNull($this->lexer->getError(), "Should be NULL by default");
    }

    public function testLexerAcceptsSingleDefinition()
    {
        $tokenDef = new Token("regex", "identifier");

        try {
            $lexer = new Lexer($tokenDef);
        } catch (Exception $e) {
            $this->fail("Should accept single token definition");
        }
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

    public function testLexerDoesNotAcceptTokenDefinitionWithEmptyRegex()
    {
        $this->setExpectedException("InvalidArgumentException");

        // Prepare token definition
        $tokenDef = array(
            new Token("", "identifier"),
        );

        $lexer = new Lexer($tokenDef);
    }

    ////////////////////////////////////////////////////////////////////////
    // Tokenizing 
    ////////////////////////////////////////////////////////////////////////

    public function testTokenizesSingleWord()
    {
        $text = "hallo";
        $identifier = "T_Hello";

        $definition = $this->createTokenDefinition("hallo", $identifier);

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Only one token in string. Not possible to parse more.");

        // Test the returned token
        $token = $tokens[0];
        $this->assertTrue(is_a($token, "SM\Lexer\Token"), "Should be a object of class Token.");
        $this->assertEquals(1, $token->getLine());
        $this->assertEquals(0, $token->getOffset());
        $this->assertEquals($identifier, $token->getIdentifier());
        $tokenMatch = $token->getMatch();
        $this->assertEquals("hallo", $tokenMatch[0]);
    }

    public function testIgnoresLeadingWhitespaces()
    {
        $text = "   hallo";
        $identifier = "T_HELLO";

        $definition = $this->createTokenDefinition("hallo", $identifier);

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens);
        $token = $tokens[0];
        $this->assertEquals(1, $token->getLine());
        $this->assertEquals(3, $token->getOffset());
        $tokenMatch = $token->getMatch();
        $this->assertEquals("hallo", $tokenMatch[0]);
    }

    public function testReturnsEmptyArrayWhenEmptyStringIsTokenized()
    {
        $text = "";

        $definition = $this->createTokenDefinition("hallo", "T_HELLO");

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(0, $tokens, "Should return empty array");
    }
 
    public function testReturnsEmptyArrayWhenWhitespaceOnlyStringIsTokenized()
    {
        $text = "        ";

        $definition = $this->createTokenDefinition("hallo", "T_HELLO");

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(0, $tokens, "Should return empty array");
    }   

    public function testTokenize2TokensOfSameTypeInARow()
    {
        $text = "hallohallo";
        $identifier = "T_HELLO";

        $definition = $this->createTokenDefinition("hallo", $identifier);

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(2, $tokens, "Should have got 2 tokens out of the text");
        foreach ($tokens as $token){
            $this->assertEquals($identifier, $token->getIdentifier(), "Should be of type T_HELLO");
        }
    }

    public function testTokenize2TokensOfSameTypeInARowWithWhitespaces()
    {
        $text = "hallo  hallo";
        $identifier = "T_HELLO";

        $definition = $this->createTokenDefinition("hallo", $identifier);

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(2, $tokens, "Should have got 2 tokens out of the text");
        foreach ($tokens as $token){
            $this->assertEquals($identifier, $token->getIdentifier(), "Should be of type T_HELLO");
        }
    }

    public function testTokenizesWithCaptureGroups()
    {
        $text = "INT: 123";
        $identifier = "T_INTEGER";

        $definition = $this->createTokenDefinition("INT: (\d+)", $identifier);

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should match a number in the text");

        $token = $tokens[0];
        $match = $token->getMatch();
        $this->assertEquals($text, $match[0]);
        $this->assertEquals(123, $match[1], "Capture group should have matched numbers");
    }

    ////////////////////////////////////////////////////////////////////////
    // Error reporting of lexer
    ////////////////////////////////////////////////////////////////////////

    public function testTokenizeReturnsFalseWhenErrorOccurs()
    {
        $tokenDef = $this->createTokenDefinition("regex", "identifier");
        $lexer = new Lexer($tokenDef);

        $result = $lexer->tokenize("blaba bla");

        $this->assertFalse($result, "Should return false if lexer cannot tokenize");
    }
    
    public function testTokenizeHasErrorWhenTokenizingFailed()
    {
        $tokenDef = $this->createTokenDefinition("regex", "identifier");
        $lexer = new Lexer($tokenDef);

        $result = $lexer->tokenize("blaba bla");

        $error = $lexer->getError();
        $this->assertTrue(is_array($error), "Error object should be array");
        $this->assertEquals(1, $error['line']);
        $this->assertEquals(0, $error['offset']);
        $this->assertNotNull($error['description']);
    }

    public function testErrorIsResetWhenNewTextIsTokenized()
    {
        $tokenDef = $this->createTokenDefinition("regex", "identifier");
        $lexer = new Lexer($tokenDef);

        $result = $lexer->tokenize("blaba bla");

        $error = $lexer->getError();
        $this->assertNotNull($error, "Should have error");

        // Start new lexing
        $result = $lexer->tokenize("regex");

        $this->assertTrue(is_array($result), "Should be array of tokens.");
        $this->assertNull($lexer->getError(), "Error should have disappeared");
    }

    ////////////////////////////////////////////////////////////////////////
    // Custom Token classes
    ////////////////////////////////////////////////////////////////////////
    
    public function testLexerReturnsObjectsOfCustomTokenClasses()
    {
        $text = "regex";

        $definition = new CustomToken();

        $lexer = new Lexer($definition);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should get one token out of it");
        $token = $tokens[0];

        $this->assertTrue(
            is_a($token, "SM\Lexer\CustomToken"), 
            "Should return object of same type as definition."
        );
    }
    

    ////////////////////////////////////////////////////////////////////////
    // Helper Methods
    ////////////////////////////////////////////////////////////////////////
    
    /**
     * Creates a token that can be used as a definition. 
     *
     * @param string $regex         The regex that is used for the token
     * @param string $identifier    The identifier of the token.
     */
    protected function createTokenDefinition($regex, $identifier)
    {
        $tokenDef = new Token($regex, $identifier);
        return $tokenDef;
    }
    
}   
