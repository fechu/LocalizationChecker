<?php

namespace LocalizationChecker\Lexer;

use SM\Lexer\Lexer;
use LocalizationChecker\Lexer\CommentToken;

class CommentTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommentToken
     */
    protected $token;

    public function setup()
    {
        $this->token = new CommentToken();
    }

    public function testIdentifierIsComment()
    {
        $this->assertEquals(CommentToken::$identifier, $this->token->getIdentifier());
    }


    ////////////////////////////////////////////////////////////////////////
    // Comment matching
    ////////////////////////////////////////////////////////////////////////

    public function testMatchesComment()
    {
        $text = "/* this is a comment */";

        $lexer = new Lexer($this->token);
        $tokens = $lexer->tokenize($text);

        $this->assertCount(1, $tokens, "Should have get one comment out of it");
    }

    public function testGetCommentReturnsComment()
    {   
        $text = "this text inside should be returned";
        $comment = "/* " . $text . " */";

        $lexer = new Lexer($this->token);
        $tokens = $lexer->tokenize($comment);

        $token = $tokens[0];
        $this->assertEquals($text, $token->getComment(), "Should match comment in between");
    }
}
