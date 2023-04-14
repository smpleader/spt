<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view model
 * 
 */

namespace SPT\Web\MVVM;

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

    public static function prepareVM($className, $xlayout, $container = null)
    {
        if(is_array($xlayout))
        {
            foreach($xlayout as $layout)
            {
                static::prepareVM($className, $layout, $container);
            }
        }
        elseif(is_string($xlayout))
        {
            $helper = static::getInstance();
            $helper->assignContainer($container);
            $helper->assignVM($className, $xlayout);
        }
    }

    public static function deployVM($layout, &$data, $viewData)
    {
        $helper = static::getInstance();
        $tryData = $helper->getData($layout, $viewData);
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

    public function assignVM($vmName, $layout)
    {
        if( 0 !== strpos($layout, 'layouts.' ) && 0 !== strpos($layout, 'widgets.') && 0 !== strpos($layout, 'vcoms.' ))
        {
            $layout = 'layouts.'. $layout;
        }

        if(!isset($this->vms[$layout]))
        {
            $this->vms[$layout] = [];
        }

        $try = explode('|', $layout);
        if( sizeof($try) > 1)
        {
            $layout = array_shift($try);
            $this->vms[$layout][] = [$vmName, $try];
        }
        else
        {
            $try = explode('.', $layout);
            $try = end( $try );
            $this->vms[$layout][] = [$vmName, [$try]];
        }

        if( is_a( $this->container, 'IApp' ) )
        {
            if(!isset($this->vmInstances[$vmName]))
            {
                $this->vmInstances[$vmName] = new $vmName;
            }
        }
        else
        {
            $this->container->set($vmName, new $vmName($this->container));
        }
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

    public function getData($layout, $viewData)
    {
        $data = [];
        if(isset($this->vms[$layout]))
        {
            foreach($this->vms[$layout] as $array)
            {
                list($vm, $functions) = $array;
                 
                $ViewModel = $this->getVM($vm);
                foreach($functions as $fnc)
                {   
                    if(!method_exists($ViewModel, $fnc))
                    {
                        throw new \Exception('Invalid function '. $fnc. ' of ViewModel '.$vm);
                    }
                    $data = array_merge($data, $ViewModel->$fnc($data, $viewData));
                }
            }
        }
        return $data;
    }
}