<?php

namespace LocalizationChecker\File;

use LocalizationChecker\File\StringsFile;
use LocalizationChecker\Lexer\TranslationEntryToken;
use SM\Lexer\Token;

/**
 * @author Sandro Meier
 */
class StringsFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringsFile
     */
    protected $file;

    public function setup()
    {
        $translationToken = new TranslationEntryToken();
        $translationToken->setMatch(array(
            '"key" = "a Translation";',
            'key', 
            'a Translation'
        ), 1, 1);
        $tokens = array(
            $translationToken
        );
        $this->file = new StringsFile($tokens);
    }

    public function testFileCreated()
    {
        $this->assertNotNull($this->file);
    }

    public function testCreationAcceptsOnlyCommentAndTranslationEntryTokens()
    {
        $this->setExpectedException("InvalidArgumentException");

        $tokens = array(
            new Token("regex", "identifier"),
        );

        $file = new StringsFile($tokens);   // Should throw exception
    }

    public function testGetTranslationKeysReturnsKeys()
    {
        $keys = $this->file->getTranslationKeys();

        $this->assertCount(1, $keys, "Should contain 1 key");
        $this->assertEquals("key", $keys[0], "Key should match the key in the translation token");
    }

    public function testContainsKeyReturnsFalseIfKeyIsNotInFile()
    {
        $this->assertFalse($this->file->containsTranslationKey("another.key"));
    }

    public function testContainsKeyReturnsTrueIfKeyIsInFile()
    {
        $this->assertTrue($this->file->containsTranslationKey("key"));
    }
    
}

