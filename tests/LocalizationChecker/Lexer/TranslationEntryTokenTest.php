<?php

namespace LocalizationChecker\Lexer;

use LocalizationChecker\Lexer\TranslationEntryToken;
use SM\Lexer\Lexer;

class TranslationEntryTokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LocalizationChecker\Lexer\TranslationEntryToken
     */
    protected $token;

    public function setup()
    {
        $this->token = new TranslationEntryToken();
    }

    public function testTokenCreated()
    {
        $this->assertNotNull($this->token);
    }

    public function testIdentifierIsTranslationKeyIdentifier()
    {
        $this->assertEquals(
            TranslationEntryToken::$identifier,
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

        $tokenDef = new TranslationEntryToken();
        $lexer = new Lexer($tokenDef);

        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should only have matched one token");

        $token = $tokens[0];
        $this->assertInstanceOf("LocalizationChecker\Lexer\TranslationEntryToken", $token);
    }

    public function testMatchesTranslationKeyWithWhitespaces()
    {
        $text = '"my.key" = "My key translation";';

        $tokenDef = new TranslationEntryToken();
        $lexer = new Lexer($tokenDef);

        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should only have matched one token");

        $token = $tokens[0];
        $this->assertInstanceOf("LocalizationChecker\Lexer\TranslationEntryToken", $token);
    }

    public function testGetKeyAfterMatching()
    {
        $text = '"my.key" = "My key translation";';

        $tokenDef = new TranslationEntryToken();
        $lexer = new Lexer($tokenDef);

        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should only have matched one token");

        $token = $tokens[0];
        $this->assertEquals("my.key", $token->getTranslationKey());
    }
    
    public function testGetValueAfterMatching()
    {
        $text = '"my.key" = "My key translation";';

        $tokenDef = new TranslationEntryToken();
        $lexer = new Lexer($tokenDef);

        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should only have matched one token");

        $token = $tokens[0];
        $this->assertEquals("My key translation", $token->getTranslationValue());
    }
}
