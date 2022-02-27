<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a view, used in MVC or ViewModel
 * 
 */

namespace SPT\View;

use SPT\View\Base as ViewParent;

class View extends ViewParent
{
    public function init(array $params)
    {
        list($this->theme, $this->lang) = $params;
    }
}