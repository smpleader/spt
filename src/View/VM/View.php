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

use SPT\View\Base as ViewParent;
use SPT\View\HookAdapter;
use SPT\Theme;

class View extends ViewParent
{
    protected $hook;

    public function init($language, $theme, HookAdapter $hook)
    {
        $this->theme = $theme; 
        $this->lang = $language;
        $this->hook = $hook;
    }

    public function _render($layout, $hook = '')
    {
        $layout = $this->safeName($layout);

        if( $file_layout = $this->theme->getPath($layout) )
        {
            $_keep_layout = $this->_index;
            $this->setIndex($layout);

            $this->hook->trigger($this, $layout, $hook);
            $content = $this->include($file_layout);

            $this->setIndex($_keep_layout);

            return $content;
        }

        return 'Layout '.$layout. '  not found' ;
    }
}