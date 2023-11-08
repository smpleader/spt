<?php
/**
 * SPT software - Base Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A basic object support dynamic properties
 * 
 */

namespace SPT;

class BaseObj 
{
    /**
     * Internal array
     * @var array $_vars
     */
    protected $_vars;

    /**
     * Assign value into internal variable array by key
     *
     * @param string|integer   $key  internal variable array key name
     * @param mixed    $value Assign value
     * 
     * @return void
     */ 
    public function set($key, $value)
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
    public function get($key, $default = null)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
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
