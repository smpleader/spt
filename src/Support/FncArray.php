<?php
/**
 * SPT software - Array
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Array to simplify the jobs
 * 
 */

namespace SPT\Support;

class FncArray
{
    public static function merge(&$arr1, $arr2)
    {
        foreach($arr2 as $key => $value)
        {
            if(is_array($value)){
                static::merge($arr1[$key], $value);
            } else {
                $arr1[$key] = $value;
            }
        }
    }

    public static function toString($arr, $break = "\n")
    {
        return implode( $break, $arr);
    }

    public static function isReady($arr)
    {
        return(is_array($arr) && count($arr));
    }

    public static function flat(array $arr, $dot='.', $includeNumericIndex=true, $token='')
    {
        $tmp = [];
        foreach($arr as $key=>$value)
        {
            $newToken = empty($token) ? $key : $token. $dot. $key;
            
            if( is_array($value) &&
                (static::isAssoc($value) || $includeNumericIndex)
            )
            {
                $tmp = $tmp + static::flat($value, $dot, $includeNumericIndex, $newToken);
            }
            else
            {
                $tmp[$newToken] = $value;
            }
        }
        return $tmp;
    }

    public static function isAssoc(array $arr)
    {
        return !static::isIndex( $arr);
    }

    public static function isIndex(array $arr, $checkSequential=false)
    {
        if (array() === $arr) return true;
        if($checkSequential)
        {
            return array_keys($arr) === range(0, count($arr) - 1);
        }
        else
        {
            return array_unique(array_map("is_int", array_keys($arr))) === array(true);
        }
    }
}
