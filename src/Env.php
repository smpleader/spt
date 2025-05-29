<?php
/**
 * SPT software -  File env 
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Get information from env file
 * 
 */

namespace SPT;

class Env
{
    /**
     * Internal array
     * @var array $_vars
     */
    static protected $_vars = array();

    /**
     * Get value from internal variable array by key
     *
     * @param string   $key  internal variable array key name
     * @param mixed    $default Return value $default if key not found
     * 
     * @return mixed    
     */ 
    static function get($key, $default = null)
    {
        return isset(static::$_vars[$key]) ? static::$_vars[$key] : $default;
    }

    /**
     * Pass any parameters into internal _vars
     *
     * @param string   $path path to env file
     * 
     * @return void load data from env file once
     */ 
    public static function load( string $path)
    {
        if( file_exists($path. '/.env') && !count(static::$_vars))
        {
            static::$_vars = parse_ini_file($path. '/.env');
        }
    }
}
