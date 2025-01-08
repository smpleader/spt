<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just base class for   layouts
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;

class Base
{ 
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected readonly string $__path;

    /**
    * Internal variable cache a token: plugin:type:path
    * @var string $_id
    */
    protected readonly string $__id;

    /**
    * Theme info
    * @var string $_path
    */
    protected Theme $theme;

    /**
     * render content into string
     * 
     * @return string 
     */ 
    public function _render(): string
    {
        ob_start();
        include $this->__path;
        $content = ob_get_clean();
        return $content;
    }

    /**
     * return current token
     * 
     * @return string 
     */ 
    public function getId(): string
    {
        return $this->__id;
    }

    /**
     * echo content to response
     * 
     * @return void 
     */ 
    public function render(): void
    {
        echo $this->_render();
    }

    /**
     * update data if new one
     * 
     * @return void 
     */ 
    public function update(array $data): void
    {
        foreach($data as $k=>$v)
        {
            if(!in_array($k, ['theme', '__path', '__id']))
            {
                if(is_callable($v))
                {
                    var_dump($k, isset($this->$k),
                    property_exists($this, $k)
                );
                    //
                    if(!isset($this->$k))
                    {
                        $v->bindTo($this, $this);
                    }
                }
                else
                {
                    $this->$k = $v;
                }
            }
        }
    }

    /**
     * magic method
     * 
     */
    public function __call($name, $args)
    {
        if (is_callable($this->$name)) {
            return call_user_func_array($this->$name, $args);
        }
        else 
        {
            throw new \RuntimeException("Method {$name} does not exist");
        }
    }
    
    /*public function __set($name, $value) 
    {
        $this->$name = is_callable($value) ? $value->bindTo($this, $this): $value;
    }*/
}