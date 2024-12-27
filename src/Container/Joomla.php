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

use Joomla\DI\Container;

class Joomla extends Container implements IContainer
{
    /**
     * Easily way to containerize a class
     */
    public function containerize(string $classname, string $fullname, \Closure $getInstance, ?string $alias = '')
    {
        if ( $this->exists($classname) && !empty($alias))
        {
            $this->alias( $alias, $fullname);
        }
        elseif ( !$this->exists($classname) && class_exists($fullname))
        {
            $ins = $getInstance($fullname, $this);
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