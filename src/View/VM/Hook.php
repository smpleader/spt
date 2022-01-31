<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SP\ViewModel\VM; 

class Hook implements HookAdapter
{
    protected $viewmodels = [];
    protected $map = [];
    
    public function init()
    {
        // fill this->map
    }

    private function getVM($view, string $name)
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
                if($vm = $this->getVM($view, $name))
                {
                    $vm->autorun($layout);
                }
            }
        }
    }
}