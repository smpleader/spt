<?php
/**
 * SPT software - Magic Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a view layout using dynamic properties
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;
use SPT\Web\View;

#[\AllowDynamicProperties]
class Dynamic extends Base
{
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected readonly string $__path;

    /**
     * update data if new one
     * 
     * @return void 
     */ 
    public function update(array $data, bool $isMethod = false): void
    {
        foreach($data as $k=>$v)
        {
            if(!in_array($k, ['theme', '__path', '__pid', '__view']))
            {
                $this->$k = is_callable($v) ? $v->bindTo($this): $v;
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
        return $this->$name ?? $this->__view->getData($name);
    }

    /**
     * magic method call function
     * 
     * @return void 
     */ 
    public function __call($name, $args)
    {
        if(is_callable($this->$name))
        {
            return call_user_func_array( $this->$name, $args);
        }
        else 
        {
            throw new \RuntimeException("Method {$name} does not exist");
        }
    }
}