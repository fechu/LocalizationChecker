<?php

namespace SM\Lexer;


use SM\Lexer\Token;

/**
 * Class CustomToken
 */
class CustomToken extends Token
{
    public function __construct()
    {
        parent::__construct("regex", "T_COSTUM");
    }
}

