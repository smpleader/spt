<?php
/**
 * SPT software - View Hook
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a View Hook
 * 
 */

namespace SPT\View\VM\JDIContainer;

use SPT\Support\FncString;
use SPT\Support\Filter;
use SPT\Support\Loader;
use SPT\View\Adapter as View;
use SPT\App\Adapter as Application;
use SPT\App\Instance as AppIns;
use SPT\View\VM\HookAdapter;
use SPT\View\VM\ViewModelAdapter;

class ViewHook implements HookAdapter
{
    protected $viewmodels = [];
    protected $map = [];
    protected $app;
    
    public function __construct(Application $app)
    {
        $this->app = $app;
        // the loader much be set when load a plugin

        $container = $this->app->getContainer();
        foreach($container->getKeys() as $name)
        {
            if($vm = $this->getVm($name))
            {
                $layouts = $vm->parse();
                foreach($layouts as $layout)
                {
                    if(isset($this->map[$layout]))
                    {
                        $this->map[$layout][] = $name;
                    }
                    else
                    {
                        $this->map[$layout] = [$name];
                    }
                }
            }
        }
    }

    protected function getVM(string $name)
    {
        $id = Filter::cmd($name);

        if('VM' == substr($id, -2))
        {
            $container = $this->app->getContainer();
    
            if($container->has($id))
            {
                $try = $container->get($id);
                if( $try instanceof ViewModelAdapter)
                {
                    return $try;
                }
            }
        }

        return false;
    }

    public function trigger(View $view, string $layout, string $hook = '')
    {
        if( isset($this->map[$layout]))
        {
            foreach($this->map[$layout] as $name)
            {
                if($vm = $this->getVM($name))
                {
                    $vm->setView($view);
                    $vm->autorun($layout);
                }
            }
        }
    }
}