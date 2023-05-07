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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Symfony extends ContainerBuilder implements IContainer
{
    public function set($name, $class)
    {
        return is_string($class) ? $this->register($name, $class) :  parent::set($name, $class);
    }

    public function exists($name)
    {
        return null !== $this->$name;
    }
}