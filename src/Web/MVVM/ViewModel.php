<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view model
 * 
 */

namespace SPT\Web\MVVM;

use SPT\Web\Theme;
use SPT\Web\ViewLayout;

class ViewModel
{
    protected $container;
    public function __construc($container = null)
    {
        $this->container = $container;
    }
}