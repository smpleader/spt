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
        return null === $x ? $key : $x;
    }

    public static function e($key){
        echo static::_($key);
    }
    
    public static function loaded()
    {
        return count(static::$_vars);
    }
}
