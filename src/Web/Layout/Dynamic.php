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

    /**
     * magic method
     * 
     */
    
    /*public function __set($name, $value) 
    {
        $this->$name = is_callable($value) ? $value->bindTo($this, $this): $value;
    }*/
}