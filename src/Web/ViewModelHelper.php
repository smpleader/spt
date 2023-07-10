<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view model
 * 
 */

namespace SPT\Web;

use SPT\Web\Theme;
use SPT\Web\ViewLayout;

class ViewModelHelper
{
    private static $instance;

    private static function getInstance()
    {
        if( null === static::$instance )
        {
            static::$instance = new ViewModelHelper;
        }

        return static::$instance;
    }

    public static function prepareVM($type, $className, $xlayout, $container = null)
    {
        if(is_array($xlayout))
        {
            foreach($xlayout as $layout)
            {
                static::prepareVM($type, $className, $layout, $container);
            }
        }
        elseif(is_string($xlayout))
        {
            $helper = static::getInstance();
            $helper->assignContainer($container);
            $helper->assignVM($type, $className, $xlayout);
        }
    }

    public static function deployVM($type, $layout, &$data, $viewData)
    {
        $helper = static::getInstance();
        $tryData = $helper->getData($type, $layout, $viewData);
        if(count($tryData))
        {
            $data = array_merge($tryData, $data);
        }
    }

    // Main helper
    private array $vms;
    private array $vmInstances;
    private $container;

    public function assignContainer($container)
    {
        if( null !== $container  )
        {
            if(!is_object($container)) //if(is_a($container, get_class($this->container)))
            {
                throw new \Exception('Invalid container type when trying to assign container');
            }

            if( null == $this->container )
            {
                $this->container = $container;
            }
        }
    }

    public function assignVM($type, $vmName, $layout)
    {
        if(!isset($this->vms[$type]))
        {
            $this->vms[$type] = [];
        }

        $try = strrpos($layout, '|');
        if(false === $try)
        {
            $try = strrpos($layout, '.');
            if(false === $try)
            {
                $try = strrpos($layout, '::');
                $func = false === $try ? $layout : substr($layout, $try+2);
            }
            else
            {
                $func = substr($layout, $try+1);
            }
        }
        else
        {
            $func = substr($layout, $try+1); 
            $layout = substr($layout, 0, $try);
        } 

        if(!isset($this->vms[$type][$layout]))
        {
            $this->vms[$type][$layout] = [];
        }
        
        $this->vms[$type][$layout][] = [$vmName, $func];
    }

    public function getVM($name)
    {
        if( is_a( $this->container, 'IApp') )
        {
            if(!isset($this->vmInstances[$name]))
            {
                throw new \Exception('Invalid View Model '. $name);
            }
            return $this->vmInstances[$name];
        }
        else
        {
            return $this->container->get($name);
        }
    }

    public function getData($type, $layout, $viewData)
    {
        $data = [];
        if(isset($this->vms[$type]) && isset($this->vms[$type][$layout]))
        {
            foreach($this->vms[$type][$layout] as $array)
            {
                list($vm, $fnc) = $array;
                 
                $ViewModel = $this->getVM($vm);
                
                if(!method_exists($ViewModel, $fnc))
                {
                    throw new \Exception('Invalid function '. $fnc. ' of ViewModel '.$vm);
                }
                $data = array_merge($data, $ViewModel->$fnc($data, $viewData));
                    
            }
        }
        return $data;
    }
}