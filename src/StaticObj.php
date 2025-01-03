<?php
/**
 * SPT software - Static Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A static object support dynamic properties
 * 
 */

namespace SPT;

use SPT\Support\FncArray as Arr;

class StaticObj 
{
    /**
     * Assign value into internal variable array by key
     *
     * @param string|integer   $key  internal variable array key name
     * @param mixed    $value Assign value
     * 
     * @return void
     */ 
    static function set( string | integer $key, $value)
    {
        static::$_vars[$key] = $value;
    }
    
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
     * Get internal variable array
     * 
     * @return array    In theory, it can be various depend on variable declaration    
     */ 
    static function getAll()
    {
        return static::$_vars;
    }
    
    /**
     * Fast way to setup internal variable array
     *
     * @param array   $arr  array to be merged into internal variable
     * 
     * @return void    
     */ 
    static function importArr(array $arr)
    {
        Arr::merge(static::$_vars, $arr);
    }
}
