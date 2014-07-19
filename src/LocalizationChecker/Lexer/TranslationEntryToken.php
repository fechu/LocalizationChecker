<?php

namespace LocalizationChecker\Lexer;

use SM\Lexer\Token;

/**
 * A token that represents a key in  a .strings file of the form:
 *
 *  "my.key" = "Die Übersetzung"
 */
class TranslationEntryToken extends Token
{
    /**
     * The identifier for a translation key token
     * 
     * Instances of TranslationKeyToken will have this set as identifier.
     */
    public static $identifier = 'T_TRANSLATION_KEY';

    public function __construct()
    {
        /// @TODO The regex failes with escaped " characters.
        $regex  = '"(.*?)"\s*=\s*"(.*?)";'; 
        parent::__construct($regex, static::$identifier);
    }

    ////////////////////////////////////////////////////////////////////////
    // Getter/Setter
    ////////////////////////////////////////////////////////////////////////
    
    /**
     * Get the key of this translation.
     */
    public function getTranslationKey()
    {
        return isset($this->match[1]) ? $this->match[1] : NULL;
    }

    /**
     * Get the value of this translation
     */
    public function getTranslationValue()
    {
        return isset($this->match[2]) ? $this->match[2] : NULL;
    }
    
    
}

