<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SP\View\VM; 

use SPT\Support\Loader;
use SPT\App\Instance as AppIns;

abstract HookBase implements HookAdapter
{
    protected $viewmodels = [];
    protected $map = [];
    
    public function init()
    {
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

    private function getVM(string $name)
    {
        // where we load ViewModels
        return false;
    }

    public function trigger($view, string $layout, string $hook = '')
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