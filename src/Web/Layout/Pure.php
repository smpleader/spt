<?php
/**
 * SPT software - Pure Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a view layout use hidden properties
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;
use SPT\Web\View;

class Pure extends Base
{
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected readonly string $__path;
    
    /**
    * Internal variable cache a token: plugin:path
    * @var string $_id
    */
    protected readonly string $__id;

    /**
    * Internal variable cache methods
    * @var array $__closures
    */
    protected  array $__closures = [];

    /**
    * Internal variable cache variables
    * @var array $__vars
    */
    protected array $__vars = [];
    
    /**
     * update data if new one
     * 
     * @return void 
     */ 
    public function update(array $data, bool $isMethod = false): void
    {
        foreach($data as $k=>$v)
        {
            if( !$isMethod && !is_callable($v))
            {
                $this->__vars[$k] = $v;
            }
            elseif( $isMethod && is_callable($v) )
            {
                $this->__closures[$k] = $v->bindTo($this);
            }
        }
    }
    
    /**
     * magic method get
     * 
     * @return mixed 
     */ 
    public function __get($name) 
    {
        if('theme' == $name) return $this->__view->_theme;
        if('config' == $name) return $this->__view->_config;
        return $this->__vars[$name] ?? $this->__view->getData($name);
    }

    /**
     * magic method set
     * 
     * @return void 
     */ 
    public function __set($name, $value) 
    {
        $this->__vars[$name] = $value;
    }
    
    /**
     * magic method call function
     * 
     * @return void 
     */ 
    public function __call($name, $args)
    {
        if(isset($this->__closures[$name]))
        {
            return call_user_func_array( $this->__closures[$name], $args);
        }
        else 
        {
            throw new \RuntimeException("Method {$name} does not exist");
        }
    }
}