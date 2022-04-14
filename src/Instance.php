<?php
/**
 * SPT software - Instance with its adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a class for Instance which has adapter
 * 
 */

namespace SPT;

abstract class Instance 
{
    protected $adapter;
    public function __construct(object $adapter)
    {
        $this->adapter = $adapter;
    }

    /* this is an idea about init:
    public function init()
    {
        $options = func_get_args();
        call_user_func_array([$this->adapter, 'init'], $options);
        return $this->adapter;
    }*/

    public function __call($method, $arguments)
    {
        if(method_exists($this->adapter, $method))
        {
            return call_user_func_array([$this->adapter, $method], $arguments);
        }
        
        return null;
    }
 
    public function __set(string $key, $value)
    {
        if(property_exists($this->adapter, $key))
        {
            $this->adapter->{$key} = $value;
        }
    }

    public function __get(string $key)
    {
        if(property_exists($this->adapter, $key))
        {
            return $this->adapter->{$key};
        }

        return null;
    }
}
