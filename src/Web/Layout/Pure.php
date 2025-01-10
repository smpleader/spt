<?php
/**
 * SPT software - Pure Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a view layout try to not use magic methods
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
    * Internal variable cache a token: plugin:type:path
    * @var string $_id
    */
    protected readonly string $__id;

    /**
    * Internal variable cache methods
    * @var array $__methods
    */
    protected  array $__methods = [];

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
    public function update(array $data): void
    {
        foreach($data as $k=>$v)
        {
            $this->__vars[$k] = $v; 
        }
    }
    
    /**
     * magic method get
     * 
     * @return mixed 
     */ 
    public function __get($name) 
    {
        return $this->__vars[$name] ?? NULL;
    }

    /*
    public function __set($name, $value) 
    {
        $this->__vars[$name] = $value;
    }*/
}