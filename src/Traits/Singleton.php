<?php
/**
 * SPT software - Pattern
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Singleton Triat
 * 
 */

namespace SPT\Traits;

trait Singleton
{
    private static $_instance;
    public static function _( $parameters = [] ){

        if( null === static::$_instance )
        {
            static::$_instance = new static($parameters);
            static::$_instance->_setup_instance();
        }

        return static::$_instance;
    }

    protected function _setup_instance(){}
}