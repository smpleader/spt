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
    /**
     * Internal var to cache an instance
     * 
     * @var  object $_instance
     */ 
    private static $_instance;

    /**
     * Get an instance by a parameter
     *
     * @param mixed   $parameters Input to bootstrap a singleton
     * 
     * @return object
     */ 
    public static function _( $parameters = [] ){

        if( null === static::$_instance )
        {
            static::$_instance = new static();
            static::$_instance->_setup_instance($parameters);
        }

        return static::$_instance;
    }

    /**
     * A hook to support the parameters
     * This support load the dependencies
     * 
     * @param mixed   $parameters Input to bootstrap a singleton
     * 
     * @var void
     */ 
    protected function _setup_instance($parameters = []){}
}