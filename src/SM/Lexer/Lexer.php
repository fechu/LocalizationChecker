<?php

namespace SM\Lexer;

/**
 * A lexer that takes token definitions and parses a text for these tokens.
 */
class Lexer
{
    /**
     * The tokens the lexer can parse.
     *
     * The tokens will be parsed in the order given in this array. So make sure that more 
     * specified tokens are before less specified tokens. E.g: "root" should be before "[a-z]+". 
     * Otherwise the text "root" gets captured by "[a-z]+". 
     */
    public $tokenDefinitions;
    
    /**
     * Construct a lexer with tokens as definition. 
     *
     * @param array $tokenDefinitions   An array of tokens, (inherit from SM\Lexer\Token). They 
     *                                  need to have a regex and an identifier. If they don't 
     *                                  behaviour is not defined.
     *
     *  @throws InvalidArgumentException    If 
     */
    public function __construct($tokenDefinitions)
    {
        if (!is_array($tokenDefinitions)) {
            throw new \InvalidArgumentException("tokenDefinitions need to be an array.");
        }

        // Check if all tokenDefinitions are correct objects
        $i = 0;
        foreach ($tokenDefinitions as $tokenDef){
            if (!is_a($tokenDef, "SM\Lexer\Token")) {
                throw new \InvalidArgumentException(
                    "Token definition at index " . $i . " is invalid. No subclass of Token."
                );
            }
            $i++;
        }
        
        // Everything ok with the token definitions!
        $this->tokenDefinitions = $tokenDefinitions;
    } 

    /**
     * Tokenize the given text. 
     *
     * @param string $text  The text that should be tokenized
     *
     * @throws Exception    When the given text cannot be parsed. The exception message
     *                      contains additional details about the error that occured.
     * @return array        An array of SM\Lexer\Token objects. 
     */
    public function tokenize( $text )
    {
        
    }

    ////////////////////////////////////////////////////////////////////////
    // Getter & Setter
    ////////////////////////////////////////////////////////////////////////

    /**
     * Gets the value of tokenDefinitions
     *
     * @return array The array of token definitions. (SM\Lexer\Token objects)
     */
    public function getTokenDefinitions()
    {
        return $this->tokenDefinitions;
    }
}

