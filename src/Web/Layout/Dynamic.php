<?php
/**
 * SPT software - Magic Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a view layout using magic methods
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
    * Internal variable cache a token: plugin:type:path
    * @var string $_id
    */
    protected readonly string $__id; 

    public function __construct(Theme $theme, string $id, string $path)
    {
        if(!file_exists($path))
        {
            throw new \Exception('Can not create a layout from path '.$path);
        }
        
        $this->__view = $view;
        $this->__path = $path;
        $this->__id = $id; 

        $this->theme = &$theme;
    }

    /**
     * update data if new one
     * 
     * @return void 
     */ 
    public function update(array $data, bool $isMethod = false): void
    {
        foreach($data as $k=>$v)
        {
            if(!in_array($k, ['theme', '__path', '__id']))
            {
                $this->$k = is_callable($v) ? $v->bindTo($this): $v;
            }
        }
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