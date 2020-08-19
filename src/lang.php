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

use StaticObj;

class Lang extends StaticObj
{
    static protected $_vars = array();

    public static function _($key){
        $x = self::get($key);
        // TODO debug
        return $x === null ? $key : $x;
    }

    public static function e($key){
        echo self::_($key);
    }
}
