<?php
/**
 * SPT software - Pattern
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Singletons Triat
 * 
 */

namespace SPT\Triat;

trait Singletons
{
    private static $_instances = [];
    public static function _( $key, $parameters = [] ){

        if( !isset( static::$_instances[$key] )
        {
            static::$_instances[$key] = static::_setup_instance($key, $parameters);
        }

        return static::$_instances[$key];
    }

    protected static function _setup_instance($key, $parameters){}
}