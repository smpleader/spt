<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller
 * 
 */

namespace SPT\Web;

use SPT\Application\IApp;
use SPT\Container\Client;   

class ControllerMVVM extends Controller
{
    protected $supportMVVM = true;

    public function toHtml()
    {
        $this->registerViewModels();
        return parent::toHtml();
    }

    public function toAjax()
    {
        $this->registerViewModels();
        return parent::toAjax();
    }

    public function registerViewModels(array $vms = array())
    { 
        $this->getOverrideLayouts();
        
        if(!count($vms))
        {
            $plgName = $this->app->get('currentPlugin');
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
