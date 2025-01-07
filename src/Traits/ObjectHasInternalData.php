<?php
/**
 * SPT software - Object has internal data
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: If object just host  data which is not belong to its properties 
 * 
 */

namespace SPT\Traits;

use SPT\Support\Filter;

trait ObjectHasInternalData
{   
    /**
     * Internal array
     * @var array $_vars
     */
    protected array $_vars = [];

    /**
     * Check internal variable exists  by key
     *
     * @param string|int   $key  internal variable array key name
     * 
     * @return mixed    value 
     */ 
    public function exists( string | int $key)
    {
        return isset($this->_vars[$key]);
    }

    /**
     * Assign value into internal variable array by key
     *
     * @param string|int   $key  internal variable array key name
     * @param mixed    $value Assign value
     * 
     * @return void
     */ 
    public function set( string | int $key, $value)
    {
        $this->_vars[$key] = $value;
    }

    /**
     * Get value from internal variable array by key
     *
     * @param string|int   $key  internal variable array key name
     * @param mixed    $default Value if key not found
     * @param nullable string    $format Value filter key
     * 
     * @return mixed
     */ 
    public function get( string | int $key, $default = null, ?string $format = null)
    {
        if(!isset($this->_vars[$key])) return $default;
        return $format ? Filter::$format($this->_vars[$key]) : $this->_vars[$key];
    }

    /**
     * Get internal variable array
     * 
     * @return array
     */ 
    public function getAll()
    {
        return $this->_vars;
    }
}