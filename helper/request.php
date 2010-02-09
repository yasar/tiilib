<?php


class TiiHlpRequest{
    public static function P($key,$default=null){return self::Post($key,$default);}
    public static function Post($key,$default=null){return self::_($key,$default,$_POST);}

    public static function G($key,$default=null){return self::Get($key,$default);}
    public static function Get($key,$default=null){return self::_($key,$default,$_GET);}
    
    public static function Any($key,$default=null){
        if(false !== ($found = self::P($key))) return $found;
        if(false !== ($found = self::G($key))) return $found;
        if(! is_null($default)) return $default;
        return false;
    }
    
    private static function _($key,$default, $source){
        if(isset($source[$key])) return $source[$key];
        if(! is_null($default)) return $default;
        return false;
    }
}

class TiiR extends TiiHlpRequest{}