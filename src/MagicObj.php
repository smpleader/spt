<?php
/**
 * SPT software - Null Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a simple object return null property when not found
 * 
 */

namespace SPT;

class MagicObj 
{
    protected $_vars;
    protected $_default;

    public function __construct($default)
    {
        $this->_vars = [];
        $this->_default = $default;
    }

    public function __set(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }

    public function __get(string $key)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $this->_default;
    }
}
