<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a view, used in MVC or ViewModel
 * 
 */

namespace SPT\MVC\Plugin;

use SPT\View\Base as ViewParent;

class View extends ViewParent
{
    public function init($language, $theme)
    {
        $this->theme = $theme; 
        $this->lang = $language;
    }
}