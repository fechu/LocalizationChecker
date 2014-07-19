<?php

namespace LocalizationChecker\Lexer;

use LocalizationChecker\Lexer\TranslationKeyToken;
use SM\Lexer\Lexer;

class TranslationKeyTokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LocalizationChecker\Lexer\TranslationKeyToken
     */
    protected $token;

    public function setup()
    {
        $this->token = new TranslationKeyToken();
    }

    public function testTokenCreated()
    {
        $this->assertNotNull($this->token);
    }

    public function testIdentifierIsTranslationKeyIdentifier()
    {
        $this->assertEquals(
            TranslationKeyToken::$identifier,
            $this->token->getIdentifier(),
            "Identifier does not match expected translation identifier"
        );
    }

    ////////////////////////////////////////////////////////////////////////
    // Translation key matching
    ////////////////////////////////////////////////////////////////////////

    public function testMatchesTranslationKey()
    {
        $text = '"my.key"="My key translation";';

        $tokenDef = new TranslationKeyToken();
        $lexer = new Lexer($tokenDef);

        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should only have matched one token");

        $token = $tokens[0];
        $this->assertInstanceOf("LocalizationChecker\Lexer\TranslationKeyToken", $token);
    }

    public function testMatchesTranslationKeyWithWhitespaces()
    {
        $text = '"my.key" = "My key translation";';

        $tokenDef = new TranslationKeyToken();
        $lexer = new Lexer($tokenDef);

        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should only have matched one token");

        $token = $tokens[0];
        $this->assertInstanceOf("LocalizationChecker\Lexer\TranslationKeyToken", $token);
    }
    
}
