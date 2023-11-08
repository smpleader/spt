<?php
/**
 * SPT software - Abstract Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Abstract for an instance which has adapter
 *                  This is useful for Service provider pattern
 * 
 */

namespace SPT;

abstract class Instance 
{
    /**
     * Internal array
     * @var object $adapter
     */
    protected $adapter;

    /**
     * A constructor
     *
     * @param object   $object  An adapter is a object
     * 
     * @return void
     */ 
    public function __construct(object $object)
    {
        $this->adapter = $adapter;
    }

    /**
     * A magic call
     *
     * @param string   $method  A method name, from adapter
     * @param array   $arguments  An array of arguments
     * 
     * @return void     Default return null if method not found
     */ 
    public function __call(string $method, $arguments)
    {
        if(method_exists($this->adapter, $method))
        {
            return call_user_func_array([$this->adapter, $method], $arguments);
        }
        
        return null;
    }
 
    /**
     * A magic set
     *
     * @param string   $key  A key name of a property from adapter
     * @param mixed   $value  value of a property from adapter
     * 
     * @return void
     */ 
    public function __set(string $key, $value)
    {
        if(property_exists($this->adapter, $key))
        {
            $this->adapter->{$key} = $value;
        }
    }
 
    /**
     * A magic get
     *
     * @param string   $key  A key name of a property from adapter
     * 
     * @return mixed    Return value of a property from adapter, null if property not found
     */ 
    public function __get(string $key)
    {
        if(property_exists($this->adapter, $key))
        {
            return $this->adapter->{$key};
        }

        return null;
    }
}
