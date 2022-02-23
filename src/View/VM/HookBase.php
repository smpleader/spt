<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SPT\View\VM; 

use SPT\Support\FncString;
use SPT\Support\Filter;
use SPT\Support\Loader;
use SPT\View\Adapter as View;
use SPT\App\Adapter as Application;
use SPT\App\Instance as AppIns;

class HookBase implements HookAdapter
{
    protected $viewmodels = [];
    protected $map = [];
    protected $app;
    
    public function init(Application $app)
    {
        $this->app = $app;
        
        if(AppIns::path('viewmodel'))
        {
            $list = Loader::findClass(AppIns::path('viewmodel'));
            
            foreach($list as $name)
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
    }

    protected function getVM(string $name)
    {
        $id = Filter::cmd(strtolower($name));
        if(isset($this->viewmodels[$id]))
        {
            return $this->viewmodels[$id];
        }

        $plugin = AppIns::main()->get('plugin', '');

        $className = empty($plugin) ? 'viewmodels\\'. FncString::uc($name)
            : 'plugins\\'. $plugin. '\viewmodels\\'. FncString::uc($name);

        $className = AppIns::main()->getName( $className );

        if(class_exists($className))
        {
            $this->viewmodels[$id] = new $className($this->app);
            return $this->viewmodels[$id];
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