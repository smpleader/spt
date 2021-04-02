<?php
/**
 * SPT software - Language 
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to support multi language
 * 
 */

namespace SPT;

use SPT\StaticObj;

class Lang extends StaticObj
{
    static protected $_vars = array();

    public static function _($key){
        $x = static::get($key);
        // TODO debug
        return null === $x ? $key : $x;
    }

    public static function e($key){
        echo static::_($key);
    }
    
    public static function _s(){
        $arr = func_get_args();
        $arr[0] = static::_($arr[0]);
        return forward_static_call_array('sprintf', $arr);
    }

    public static function s(){
        $arr = func_get_args();
        $arr[0] = static::_($arr[0]);
        echo forward_static_call_array('sprintf', $arr);
    }
    
    public static function loaded()
    {
        return count(self::$_vars);
    }
}
