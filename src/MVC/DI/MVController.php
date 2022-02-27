<?php
/**
 * SPT software - MVC Controller with simple DI
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: MVC Controller
 * 
 */

namespace SPT\MVC\DI;

use SPT\BaseObj;
use SPT\View\Theme;  
use SPT\View\VN\View;  
use SPT\View\VM\DI\ViewHook;  
use SPT\App\Adapter as Application;
use SPT\App\Instance as AppIns;

class MVController extends Controller
{
    public function prepareView()
    {   
        $this->view = new View();
        $this->view->init([
            $this->app->lang, 
            $this->prepareTheme(),
            new ViewHook
        ]);
    }
}