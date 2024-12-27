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
    
    /**
     * Easily way to containerize a class
     */
    public function containerize(string $classname, string $fullname, object | \Closure $getInstance, ?string $alias = '')
    {
        if ( $this->exists($classname) && !empty($alias))
        {
            $this->alias( $alias, $fullname);
        }
        elseif ( !$this->exists($classname) && class_exists($fullname))
        {
            $ins = is_callable($getInstance) ? $getInstance($fullname, $this) : $getInstance;
            
            if(!($ins instanceof $fullname))
            {
                throw new \RuntimeException('Invalid object when containerize '. $classname);
            } 

            $this->share( $classname, $ins, true);

            if(!empty($alias))
            {
                $this->alias( $alias, $fullname);
            }
        }
    }
}