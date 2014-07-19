<?php

namespace LocalizationChecker\Lexer;

use SM\Lexer\Token;

/**
 * A token that represents a key in  a .strings file of the form:
 *
 *  "my.key" = "Die Übersetzung"
 */
class TranslationKeyToken extends Token
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
}

