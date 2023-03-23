<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Base abstract implement Container
 * 
 */

namespace SPT\Container; 

use Illuminate\Container\Container;

class Laravel extends Container
{
    public function set($name, $class)
    {
        return $this->instance($name, $class);
    }
}