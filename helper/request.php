<?php


class TiiHlpRequest{
    static public function P($key,$default=null){return self::Post($key,$default);}
    static public function Post($key,$default=null){return self::_($key,$default,$_POST);}

    static public function G($key,$default=null){return self::Get($key,$default);}
    static public function Get($key,$default=null){return self::_($key,$default,$_GET);}
    
    static public function Any($key,$default=null){
        if(false !== ($found = self::P($key))) return $found;
        if(false !== ($found = self::G($key))) return $found;
        if(! is_null($default)) return $default;
        return false;
    }
    
    static private function _($key,$default, $source){
        if(isset($source[$key])) return $source[$key];
        if(! is_null($default)) return $default;
        return false;
    }
}

class TiiR extends TiiHlpRequest{}