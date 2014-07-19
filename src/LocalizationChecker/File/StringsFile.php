<?php

namespace LocalizationChecker\File;

/**
 * Class StringsFile
 * @author Sandro Meier
 */
class StringsFile
{
    /**
     * The tokens of which the file consists.
     */
    protected $tokens;

    /**
     * An array containing all translation keys in the file. (Only the keys)
     *
     * Example:
     * When the following entry is in the file, only "key" will be in this array.
     * "key" = "A translation";
     */
    protected $translationKeys;

    /**
     * Create a new strings file representation based on an array of tokens. 
     *
     * @param array $tokens  An array of CommentToken and TranslationEntryToken objects.
     * @throws InvalidArgumentException If the token array contains not allowed objects.
     */
    public function __construct($tokens)
    {
        // Check the tokens
        foreach ($tokens as $token){
            if (!is_a($token, "LocalizationChecker\Lexer\CommentToken") && 
                !is_a($token, "LocalizationChecker\Lexer\TranslationEntryToken")) {
                
                throw new \InvalidArgumentException(
                    "Tokens can only be of class CommentToken or TranslationEntryToken"
                );
            }
        }
        

        $this->tokens = $tokens;
        $this->updateTranslationKeys();
    }

    /**
        * Check if the file contains a given translation key. 
        * 
        * @param string|array   $key    Either a single string or an array of strings. 
        *
        * @return bool  True if the file contains the key, false otherwise
     */
    public function containsTranslationKey($key)
    {
        return in_array($key, $this->translationKeys);
    }
    

    protected function updateTranslationKeys()
    {
        $this->translationKeys = array();

        foreach ($this->tokens as $token){
            if (is_a($token, "LocalizationChecker\Lexer\TranslationEntryToken")) {
                $this->translationKeys[] = $token->getTranslationKey();
            }
        }
        
    }

    ////////////////////////////////////////////////////////////////////////
    // Getter
    ////////////////////////////////////////////////////////////////////////

    /**
     * Gets the value of translationKeys
     *
     * @return array 
     */
    public function getTranslationKeys()
    {
        return $this->translationKeys;
    }
    
    
}


