<?php
/**
 * SPT software - A Controller using MVVM
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller with MVVM
 * 
 */

namespace SPT\Web;

use SPT\Application\IApp;
use SPT\Container\Client;   

class ControllerMVVM extends Controller
{
    /**
     * Internal variable to check if we apply MVVM or not
     * @var bool $supportMVVM
     */
    protected $supportMVVM = true;

    /**
     * Return HTML format after a process
     * 
     * @return string HTML content body
     */ 
    public function toHtml()
    {
        $this->registerViewModels();
        return parent::toHtml();
    }

    /**
     * Return a content body for ajax response
     * Mostly it's for a html layout or  non-json content
     * 
     * @return string A content body
     */ 
    public function toAjax()
    {
        $this->registerViewModels();
        return parent::toAjax();
    }

    /**
     * Register ViewModel list to current system
     *
     * @param array   $vms  ViewModel list
     * 
     * @return void 
     */ 
    public function registerViewModels(array $vms = array())
    {
        $plgName = $this->app->get('currentPlugin', '_NOT_SET_');
        if('_NOT_SET_' === $plgName)
        {
            // Carefully check SPT\Support\App::createController()
            $this->app->raiseError('Invalid current plugin');
        }
        
        if(!count($vms))
        {
            $vms = $this->app->getVMList($plgName);
        }
        $container = $this->getContainer();
        
        foreach($vms as $vm)
        {
            list($name, $fullname) = $vm;

            $registers = $fullname::register();
            if(isset($registers['layout']))
            {
                if(!$container->exists($name))
                {
                    $container->share( $name, new $fullname($container), true);
                }

                ViewModelHelper::prepareVM(
                    'layout',
                    $name, 
                    $registers['layout'], 
                    $container
                );
            }
        }
    }
}
