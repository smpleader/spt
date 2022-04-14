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
use SPT\View\VM\HookAdapter;
use SPT\View\Theme; 

class View extends ViewParent
{
    protected $hook;

    protected function getMutableFields(): array
    {
        return [
            'lang' => '',
            'theme' => '\SPT\View\Theme',
            'hook' => '\SPT\View\VM\HookAdapter',
        ];
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