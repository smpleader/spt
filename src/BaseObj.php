<?php
/**
 * SPT software - Base Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic object
 * 
 */

namespace SPT;

class BaseObj 
{
    protected $_vars;

    public function set($key, $value)
    {
        $this->_vars[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
    }

    public function getAll()
    {
        return $this->_vars;
    }

    public function init(array $vars )
    { 
        foreach($vars as $key => $val)
        {
            if( !is_numeric($key) )
            {
                $this->set($key, $val);
            }
        } 
    }
}
