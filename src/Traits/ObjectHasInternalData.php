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
     * @param string|integer   $key  internal variable array key name
     * 
     * @return mixed    value 
     */ 
    public function exists( string | integer $key)
    {
        return isset($this->_vars[$key]);
    }

    /**
     * Assign value into internal variable array by key
     *
     * @param string|integer   $key  internal variable array key name
     * @param mixed    $value Assign value
     * 
     * @return void
     */ 
    public function set( string | integer $key, $value)
    {
        $this->_vars[$key] = $value;
    }

    /**
     * Get value from internal variable array by key
     *
     * @param string|integer   $key  internal variable array key name
     * @param mixed    $default Value if key not found
     * 
     * @return mixed
     */ 
    public function get( string | integer $key, $default = null)
    {
        return  $this->_vars[$key] ?? $default;
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