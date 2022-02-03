<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Base abstract implement Container
 * 
 */

namespace SPT\ServiceProvider; 

use SPT\Support\Loader;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface; 
use SPT\Core\Application;

class Base implements ServiceProviderInterface
{
    protected $enqueue = [];
    public function add(array $array)
    {
        foreach($array as $key => $value)
        {
            if( is_array($value) )
            {
                $this->enqueue[$key] = isset($this->enqueue[$key]) ? array_merge($this->enqueue[$key], $value) : $value;
            }else
            {
                $this->enqueue[$key] = $value;
            }
        }
    }

    protected function getClassName($namespace, $kobj, $obj)
    {
        $class = $obj;
        $alias = $obj;

        if( is_array($obj) )
        {
            $class = $kobj;
            $alias = $obj['alias'];
        }
        else
        {
            $alias = end( explode('\\', $obj) );
        }
        
        $class = $namespace. '\\'. $class;
        if(!class_exists($class))
        {
            throw new \RuntimeException('Invalid Class: '.$class, 500);
        }

        return [$class, $alias];
    }

    public function init(){} 

    public function register(Container $container)
    {
        /* example for a placeholder
        $container->share(
            'SPT\Core\Application',
            new Application($container),
            true
        );

        $container->alias('app', 'SPT\Core\Application');
        */
    }
}