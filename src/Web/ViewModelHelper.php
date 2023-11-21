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
    /**
    * Internal variable to singleton current helper object
    * @var ViewModelHelper $instance
    */
    private static $instance;

    /**
     * Get Singleton class
     * 
     * @return mixed 
     */ 
    private static function getInstance()
    {
        if( null === static::$instance )
        {
            static::$instance = new ViewModelHelper;
        }

        return static::$instance;
    }

    /**
     * First input to map class VM vs layouts based array or string name of layout(s)
     * 
     * @param string   $type layout type
     * @param string   $className class name
     * @param string   $xlayout value format filter
     * @param string   $container method 
     * 
     * @return mixed 
     */ 
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

    /**
     * Process all view models relate to a layout
     * 
     * @param string   $type layout type
     * @param string   $layout layout name
     * @param array   $data array of data pass into layout
     * @param string   $viewData array of data belong to view, a shared layout 
     * 
     * @return void 
     */ 
    public static function deployVM($type, $layout, &$data, $viewData)
    {
        $helper = static::getInstance();
        $tryData = $helper->getData($type, $layout, $viewData);
        if(count($tryData))
        {
            $data = array_merge($tryData, $data);
        }
    }

    // Main: Non-static methods and properties, for a Helper

    /**
    * Internal variable to cache a map between ViewModel vs Layout
    * @var array $vms
    */    
    private array $vms;

    /**
    * Internal variable to cache the ViewModel instances, it's for the case application running without container
    * @var array $vmInstances
    */
    private array $vmInstances;

    /**
    * Internal variable to cache the container, it's for the case application running without container
    * @var array $container
    */
    private $container;

    /**
     * Adapt a container from current application to VM
     * 
     * @param object   $container a container
     * 
     * @return void 
     */     
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

    /**
     * Build a map for a ViewModel to a layout
     * 
     * @param string   $type layout type
     * @param string   $vmName ViewModel name
     * @param string   $layout layout configruation, not only layout name
     * 
     * @return void 
     */  
    public function assignVM(string $type, string $vmName, string $layout)
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

    /**
     * Get a ViewModel based its name
     * 
     * @param string   $name ViewModel name
     * 
     * @return ViewModel 
     */  
    public function getVM(string $name)
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

    /**
     * Collect data with ViewModel map
     * 
     * @param string   $type layout type
     * @param string   $type layout name
     * @param array   $viewData array data from view
     * 
     * @return array 
     */ 
    public function getData(string $type, string $layout, $viewData)
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