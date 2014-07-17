<?php

namespace SM\Lexer;


/**
 * A token that represents a part in a text. 
 *
 * The Lexer will output an array of tokens. 
 *
 * To define a custom token, you can either Subclass or just instantiate.
 */
class Token {
    
    /**
     * The Regular expression which is used to identifiy this token.
     *
     * Use the constructor to set this.
     */
    protected $regex;

    /**
     * The identifier of this token. 
     * @var string
     */
    protected $identifier;

    /**
     * The match object which is returned from the regex method. 
     */
    protected $match;

    /**
     * the line number on which this token was found. 
     */
    protected $line;

    /**
     * The offset in the line at which this token starts. 
     */
    protected $offset;


    /**
     * Create a new token with a regex.
     *
     * @param String $regex The regex which is used to identify this token.
     */
    public function __construct($regex, $identifier)
    {
        $this->regex = $regex;
        $this->identifier = $identifier;
    }

    /**
     * Sets the value of match
     *
     * @param array $match  The match that was returned from the regex function.
     * @param int   $line   The line where the token was found
     * @param int   $offset The offset where the token was found on the line 
     *
     * @return Token
     */
    public function setMatch($match, $line, $offset)
    {
        $this->match    = $match;
        $this->line     = $line;
        $this->offset   = $offset;
        return $this;
    }

    /**
     * Gets the value of regex
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Gets the value of identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Gets the value of line
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Gets the value of offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Gets the value of match
     *
     * @return array
     */
    public function getMatch()
    {
        return $this->match;
    }
}

