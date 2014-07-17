<?php

namespace LocalizationChecker\Lexer;

use SM\Lexer\Token;

/**
 * A token that represents a comment of the form /* Comment */ 
class CommentToken extends Token
{
    /**
     * The identifier for a comment.
     * 
     * Instances of CommentToken will have this set as identifier.
     */
    public static $identifier = 'T_COMMENT';

    public function __construct()
    {
        $regex = "\/\*\*?\s*(.*?)\s*\*\/";
        parent::__construct($regex, static::$identifier);
    }

    /**
     * Get the comment inside.
     *
     * @return string
     */
    public function getComment()
    {
        return isset($this->match[1]) ? $this->match[1] : "";
    }
}

