<?php
/**
 * SPT software - Magic Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An object support dynamic properties
 * 
 */

namespace SPT;

class MagicObj 
{
    /**
     * Internal array
     * @var array $_vars
     */
    protected $_vars;

    /**
     * A constructor
     * 
     * @return void
     */ 
    public function __construct()
    {
        $this->_vars = [];
    }

    /**
     * A magic set
     *
     * @param string    $key  Set array index _vars
     * @param mixed     $value  Set array value of _vars
     * 
     * @return void
     */ 
    public function __set(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }

    /**
     * A magic get
     *
     * @param string    $key  Get value by array index 
     * 
     * @return mixed    Return value from array _vars, return null if key not found
     */ 
    public function __get(string $key)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : null;
    }

    /**
     * Get all the variables in an array
     * 
     * @return mixed    Return array _vars
     */ 
    public function toArray()
    {
        return $this->_vars;
    }

    /**
     * Check if key exists
     * 
     * @return boolean 
     */ 

    public function isset($key)
    {
        return isset($this->_vars[$key]);
    }
}
