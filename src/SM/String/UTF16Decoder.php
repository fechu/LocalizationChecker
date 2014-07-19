<?php

namespace SM\String;


/**
 * Class UTF16Decoder
 * @author Sandro Meier
 */
class UTF16Decoder
{
    /**
     * Decode UTF-16 encoded strings.
     * 
     * Can handle both BOM'ed data and un-BOM'ed data. 
     * Assumes Big-Endian byte order if no BOM is available.
     * 
     * @param   string  $str  UTF-16 encoded data to decode.
     * @return  string  UTF-8 / ISO encoded data.
     * @access  public
     * @version 0.1 / 2005-01-19
     * @author  Rasmus Andersson {@link http://rasmusandersson.se/}
     * @package Groupies
     */
    static function decode( $str ) {
        if( strlen($str) < 2 ) return $str;
        $bom_be = true;
        $c0 = ord($str{0});
        $c1 = ord($str{1});
        if( $c0 == 0xfe && $c1 == 0xff ) { $str = substr($str,2); }
        elseif( $c0 == 0xff && $c1 == 0xfe ) { $str = substr($str,2); $bom_be = false; }
        $len = strlen($str);
        $newstr = '';
        for($i=0;$i<$len;$i+=2) {
            if( $bom_be ) { $val = ord($str{$i})   << 4; $val += ord($str{$i+1}); }
            else {        $val = ord($str{$i+1}) << 4; $val += ord($str{$i}); }
            $newstr .= ($val == 0x228) ? "\n" : chr($val);
        }
        return $newstr;
    }
}

