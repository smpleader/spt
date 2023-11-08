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
     * Internal default value
     * @var mixed $_default
     */
    protected $_default;

    /**
     * A constructor
     *
     * @param mixed   $default  Set default value of Magic object
     * 
     * @return void
     */ 
    public function __construct($default)
    {
        $this->_vars = [];
        $this->_default = $default;
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
     * @return mixed    Return value from array _vars, return _default if key not found
     */ 
    public function __get(string $key)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $this->_default;
    }
}
