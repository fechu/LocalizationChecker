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
        // If the argument is not an array, put it into an array.
        if (!is_array($tokenDefinitions)) {
            $tokenDefinitions = array($tokenDefinitions);
        }

        // Check if all tokenDefinitions are correct objects
        $i = 0;
        foreach ($tokenDefinitions as $tokenDef){
            // Check if it is a Token object
            if (!is_a($tokenDef, "SM\Lexer\Token")) {
                throw new \InvalidArgumentException(
                    "Token definition at index " . $i . " is invalid. No subclass of Token."
                );
            }

            // Check if the regex is non empty.
            $regex = $tokenDef->getRegex();
            if($regex == NULL || $regex == "") {
                throw new \InvalidArgumentException(
                    "Token definition at index " . $i . " is invalid. Token must contain regex."
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
     * When a part of a text is matched by a token definition, the method creates a clone
     * of the token definition and fills in the position of the match and the match itself. 
     * This token will then be added to the array that is returned.
     *
     * @param string $text  The text that should be tokenized
     *
     * @throws InvalidArgumentException     When the given text cannot be parsed. The exception 
     *                                      message contains additional details about the error 
     *                                      that occured.
     * @return array        An array of SM\Lexer\Token objects. 
     */
    public function tokenize( $text )
    {
        // Divide the text into lines
        $text = str_replace("\r\n", "\n", $text);
        $lines = explode("\n", $text);
        
        // Tokenize all lines.
        $tokens = array();
        $lineNumber = 1;
        foreach ($lines as $line){
            $lineTokens = $this->tokenizeLine($line, $lineNumber);
            $tokens = array_merge($tokens, $lineTokens);
            $lineNumber++;
        }
        
        return $tokens;
    }

    /**
     * Tokenizes a single line. 
     *
     * @param string    $line       A line of text which may not contain any newline characters.
     * @param integer   $lineNumber The number of the line that is currently parsed. 
     *
     * @return array    An array with tokens
     *
     * @throws InvalidArgumentException When the line cannot be parsed.
     */
    protected function tokenizeLine($line, $lineNumber = 1)
    {
        $tokenDefCount = count($this->tokenDefinitions);

        // variable initialization
        $i = 0;
        $match = false;
        $offset = 0;
        $tokens = array();
        
        // Check token definitions as long as we don't have one.
        while (($i < $tokenDefCount) && strlen($line) > 0) {

            // Trim the line
            $oldLineLength = strlen($line);
            $line = ltrim($line);
            $offset += $oldLineLength - strlen($line);

            $tokenDef = $this->tokenDefinitions[$i];
            $regex = "/^" . $tokenDef->getRegex() . "/";

            $matchFound = preg_match($regex, $line, $matches);
            if ($matchFound) {
                // Create a new token. We had a match.
                $token = clone $tokenDef;
                $token->setMatch($matches, $lineNumber, $offset);
                
                // Adjust offset and line string
                $matchedString = $matches[0];
                $offset += strlen($matchedString);
                $line = substr($line, $offset);

                $tokens[] = $token;

                // Reset i to start from the beginning again of the token definitions.
                $i = -1;
            }

            $i++;
        }

        // Check if the whole line was tokenized
        if (strlen($line) > 0) {
            throw new \InvalidArgumentException(
                "Could not parse token starting at offset " . $offset . " on line " . $lineNumber . 
                " No token definition found that matches: " . $line
            );
        }

        return $tokens;
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

