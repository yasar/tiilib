<?php
class TiiHlpArray
{

    /**
     * Extend() - will take arrays as parameter, and will merge them then will return the new array
     *
     * @return Array Extended array
     */
    static public function Extend()
    {
        $args = func_get_args();
        $extended = array();
        if (is_array($args) && count($args)) {
            foreach ($args as $array) {
                if (is_array($array)) {
                    $extended = array_merge($extended, $array);
                }
            }
        }
        return $extended;
    }

    static public function ExtendDeeper($array1, $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::ExtendDeeper($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;

    }
    
    
    /**
     * takes a multi-dimensional array, and flattens into a zero indexed array.
     */
    static public function Flatten($array, $flat = false){
        if (!is_array($array) || empty($array)) return ''; 
        if (empty($flat)) $flat = array(); 
            
        foreach ($array as $key => $val) { 
          if (is_array($val)) $flat = self::Flatten($val, $flat); 
          else $flat[] = $val; 
        } 
            
        return $flat; 
    }
}

class TiiA extends TiiHlpArray{}