<?php
/**
 * SPT software - Request Cookie
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Post
 * 
 */

namespace SPT\Request;

class Singleton
{
    private static $_instance;
    public static function instance(){

        if( null === static::$_instance )
        {
            static::$_instance = new Base();
        }

        return static::$_instance;
    }
}